<?php

namespace Tests\Feature\RegistrationModule;

use App\Models\User;
use App\Modules\EventModule\Models\Event as EventModel;
use App\Modules\RegistrationModule\Models\IssuedTicket;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Services\CheckoutService;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

class ConcurrentCheckoutTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        if (! in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb', 'pgsql'], true)) {
            $this->markTestSkipped(
                'Uji concurrency membutuhkan MySQL/PostgreSQL. Lihat bagian pengujian di README.',
            );
        }

        if (! function_exists('pcntl_fork')) {
            $this->markTestSkipped('Ekstensi PHP pcntl tidak tersedia untuk uji concurrency.');
        }
    }

    public function test_two_concurrent_checkouts_cannot_oversell_the_last_seat(): void
    {
        $users = User::factory()->count(2)->create();
        $event = EventModel::factory()->published()->create();
        $ticket = Ticket::factory()->forEvent($event->id)->create([
            'initial_quota' => 1,
            'remaining_quota' => 1,
        ]);

        $barrier = tempnam(sys_get_temp_dir(), 'checkout-barrier-');
        $resultFiles = [
            tempnam(sys_get_temp_dir(), 'checkout-result-a-'),
            tempnam(sys_get_temp_dir(), 'checkout-result-b-'),
        ];
        unlink($barrier);

        DB::disconnect();

        $pids = [];

        foreach ($users->values() as $index => $user) {
            $pid = pcntl_fork();

            if ($pid === -1) {
                touch($barrier);

                foreach ($pids as $runningPid) {
                    pcntl_waitpid($runningPid, $status);
                }

                $this->fail('Gagal membuat proses child untuk uji concurrency.');
            }

            if ($pid === 0) {
                DB::purge();
                Auth::loginUsingId($user->id);

                $deadline = microtime(true) + 10;

                while (! file_exists($barrier)) {
                    if (microtime(true) > $deadline) {
                        file_put_contents($resultFiles[$index], 'failed:barrier-timeout');
                        exit(1);
                    }

                    usleep(1000);
                }

                $result = 'success';

                try {
                    app(CheckoutService::class)->process($user->id, [[
                        'ticket_id' => $ticket->id,
                        'quantity' => 1,
                    ]]);
                } catch (Throwable $exception) {
                    $result = 'failed:'.get_class($exception);
                }

                file_put_contents($resultFiles[$index], $result);
                exit(0);
            }

            $pids[] = $pid;
        }

        touch($barrier);

        foreach ($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        $results = array_map(
            fn (string $file) => file_get_contents($file),
            $resultFiles,
        );

        foreach ([$barrier, ...$resultFiles] as $file) {
            @unlink($file);
        }

        $this->assertCount(1, preg_grep('/^success$/', $results), implode(' | ', $results));
        $this->assertSame(1, Registration::query()->count());
        $this->assertSame(1, IssuedTicket::query()->count());
        $this->assertSame(0, $ticket->refresh()->remaining_quota);
    }
}
