<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\Registration\EmailValidated;
use App\Notifications\Registration\EmailValidationPending;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function getValidInput(): Collection
    {
        return collect([
            'name' => 'fulanito',
            'email' => 'fulanito@email.com',
            'phone' => '12333445',
            'password' => 'Secret010!',
            'photo' => UploadedFile::fake()->image('profile.jpg', 100, 100)->size(1000),
            'notify_sms' => false,
        ]);
    }

    public function test_register_ok(): void
    {
        Notification::fake();

        $input = $this->getValidInput();
        $response = $this->post(URL::route('register'), $input->toArray());
        $response->assertSuccessful();

        $user = User::first();
        $keys = ['name', 'phone', 'email'];
        $this->assertSame(Arr::only($user->toArray(), $keys), $input->only($keys)->toArray());
        $this->assertFalse($user->is_verified);
        $this->assertFalse($user->notify_sms);
        $this->assertTrue(Storage::disk('public')->exists($user->photo));
        $this->assertTrue(Hash::check($input->get('password'), $user->password));

        Notification::assertSentTo([$user], EmailValidationPending::class);
        Notification::assertSentTo($user, EmailValidationPending::class,
            function (EmailValidationPending $notification, array $channels) use ($user) {
                $this->assertEqualsCanonicalizing($channels, ['database', 'mail']);

                $urlMailAction = str_replace('&', '&amp;', $notification->getMailActionUrl($user));
                $mailContent = $notification->toMail($user)->render();
                $this->assertStringContainsString($urlMailAction, $mailContent);

                return true;
            }
        );
    }

    public function test_register_sms_ok(): void
    {
        Notification::fake();

        $input = $this->getValidInput()->merge(['notify_sms' => true]);
        $response = $this->post(URL::route('register'), $input->toArray());
        $response->assertSuccessful();

        $user = User::first();
        $this->assertTrue($user->notify_sms);

        Notification::assertSentTo([$user], EmailValidationPending::class);
        Notification::assertSentTo($user, EmailValidationPending::class,
            function (EmailValidationPending $notification, array $channels) {
                $this->assertEqualsCanonicalizing($channels, ['database', 'mail', 'vonage']);

                return true;
            }
        );
    }

    public function test_verify_email_ok(): void
    {
        $input = $this->getValidInput();
        $response = $this->post(URL::route('register'), $input->toArray());

        $user = User::first();
        $urlMailAction = (new EmailValidationPending())->getMailActionUrl($user);
        $this->assertStringStartsWith(URL::route('validate_email'), $urlMailAction);

        Notification::fake();

        $response = $this->get($urlMailAction);
        $response->assertSuccessful();
        $response->assertViewIs('email_validated');

        $user->refresh();
        $this->assertTrue($user->is_verified);

        Notification::assertSentTo([$user], EmailValidated::class);
    }
}
