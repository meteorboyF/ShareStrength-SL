<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create conversations table if it doesn't exist
        DB::statement("
            CREATE TABLE IF NOT EXISTS conversations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_one_id BIGINT UNSIGNED NOT NULL,
                user_one_type VARCHAR(255) NOT NULL,
                user_two_id BIGINT UNSIGNED NOT NULL,
                user_two_type VARCHAR(255) NOT NULL,
                task_id BIGINT UNSIGNED NULL,
                last_message_at TIMESTAMP NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_user_one (user_one_id, user_one_type),
                INDEX idx_user_two (user_two_id, user_two_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Add columns to messages table if they don't exist
        $columns = DB::select("SHOW COLUMNS FROM messages");
        $columnNames = array_column($columns, 'Field');

        if (!in_array('conversation_id', $columnNames)) {
            DB::statement("ALTER TABLE messages ADD COLUMN conversation_id BIGINT UNSIGNED NULL AFTER id");
        }

        if (!in_array('sender_type', $columnNames)) {
            DB::statement("ALTER TABLE messages ADD COLUMN sender_type VARCHAR(255) NOT NULL DEFAULT 'user' AFTER sender_id");
        }

        if (!in_array('receiver_type', $columnNames)) {
            DB::statement("ALTER TABLE messages ADD COLUMN receiver_type VARCHAR(255) NOT NULL DEFAULT 'user' AFTER receiver_id");
        }
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS conversations");
    }
};
