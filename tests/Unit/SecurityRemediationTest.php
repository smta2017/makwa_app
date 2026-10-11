<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Helper\LocationHelper;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\payment2\paymentGateway;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SecurityRemediationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('customer_cards')) {
            Schema::create('customer_cards', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('order_id')->nullable();
                $table->integer('transaction_success')->default(0);
                $table->string('obj_id')->nullable();
                $table->string('token')->nullable();
                $table->string('masked_pan')->nullable();
                $table->string('merchant_id')->nullable();
                $table->string('card_subtype')->nullable();
                $table->string('email')->nullable();
                $table->string('user_added')->nullable();
                $table->timestamps();
            });
        }
    }

    public function testLocationHelperPointInPolygonMethodExists()
    {
        $helper = new LocationHelper();
        $this->assertTrue(method_exists($helper, 'pointInPolygon'));
    }

    public function testCustomerIsVerifiedNotFillable()
    {
        $customer = new Customer();
        $fillable = $customer->getFillable();
        $this->assertNotContains('is_verified', $fillable);
    }

    public function testPasswordResetValidationFailsWithMissingToken()
    {
        $controller = new PasswordResetController();
        $request = Request::create('/change-password', 'POST', [
            'email' => 'test@example.com',
            'password' => 'newpassword123'
        ]);
        $request->headers->set('Accept', 'application/json');

        $response = $controller->changePassword($request);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testPasswordResetFailsWithInvalidToken()
    {
        $controller = new PasswordResetController();
        $request = Request::create('/change-password', 'POST', [
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'token' => 'invalid-token-123456789'
        ]);
        $request->headers->set('Accept', 'application/json');

        $response = $controller->changePassword($request);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertStringContainsString('Invalid or expired password reset token', $response->getContent());
    }

    public function testPaymobCallbackFailsWithInvalidSignature()
    {
        config(['services.paymob.hmac_secret' => 'test_secret_key']);

        $gateway = new paymentGateway();
        $request = Request::create('/api/callbackhook', 'POST', [
            'type' => 'TRANSACTION',
            'obj' => [
                'id' => 12345,
                'pending' => 'false',
                'amount_cents' => 1000,
                'success' => 'true',
                'is_auth' => 'false',
                'is_capture' => 'false',
                'is_standalone_payment' => 'true',
                'is_voided' => 'false',
                'is_refunded' => 'false',
                'error_occured' => 'false',
                'refunded_amount_cents' => 0,
                'captured_amount_cents' => 0,
                'updated_at' => '2026-10-10T12:00:00',
                'currency' => 'EGP',
                'order' => ['id' => 999],
                'owner' => 1
            ],
            'hmac' => 'invalid_hmac_signature'
        ]);

        $response = $gateway->callback_hook($request);
        $this->assertEquals(401, $response->getStatusCode());
    }
}
