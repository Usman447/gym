<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Setting;

class AddWhatsappSettingsToTrnSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = [
            [
                'key' => 'whatsapp_automation_enabled',
                'value' => '0',
            ],
            [
                'key' => 'whatsapp_api_key',
                'value' => '',
            ],
            [
                'key' => 'whatsapp_api_secret',
                'value' => '',
            ],
            [
                'key' => 'whatsapp_from_number',
                'value' => '',
            ],
            [
                'key' => 'whatsapp_reminder_2_days',
                'value' => '5',
            ],
            [
                'key' => 'whatsapp_reminder_3_days',
                'value' => '7',
            ],
            [
                'key' => 'whatsapp_reminder_1_message',
                'value' => 'Hello {member_name}, your subscription is ending on {end_date}. Please renew to continue enjoying our services.',
            ],
            [
                'key' => 'whatsapp_reminder_2_message',
                'value' => 'Hello {member_name}, your subscription ended {days_ago} days ago. Please renew your subscription to continue.',
            ],
            [
                'key' => 'whatsapp_reminder_3_message',
                'value' => 'Hello {member_name}, this is a final reminder. Your subscription ended {days_ago} days ago. Please renew soon.',
            ],
            [
                'key' => 'whatsapp_automation_interval',
                'value' => '30',
            ],
            [
                'key' => 'whatsapp_start_time',
                'value' => '09:00',
            ],
            [
                'key' => 'whatsapp_end_time',
                'value' => '21:00',
            ],
        ];

        foreach ($settings as $setting) {
            $existing = Setting::where('key', $setting['key'])->first();
            if (!$existing) {
                DB::table('trn_settings')->insert([
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'updated_at' => DB::raw('NOW()'),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $keys = [
            'whatsapp_automation_enabled',
            'whatsapp_api_key',
            'whatsapp_api_secret',
            'whatsapp_from_number',
            'whatsapp_reminder_2_days',
            'whatsapp_reminder_3_days',
            'whatsapp_reminder_1_message',
            'whatsapp_reminder_2_message',
            'whatsapp_reminder_3_message',
            'whatsapp_automation_interval',
            'whatsapp_start_time',
            'whatsapp_end_time',
        ];

        Setting::whereIn('key', $keys)->delete();
    }
}

