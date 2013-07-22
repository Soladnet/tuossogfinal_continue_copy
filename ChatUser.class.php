<?php

class ChatUser extends ChatBase {

    protected $user_id = '', $com_id = '';

    public function save() {

        DB::query("
			INSERT INTO community_chat_online (com_id,user_id)
			VALUES (
				'" . DB::esc($this->com_id) . "',
				'" . DB::esc($this->user_id) . "'
		)ON DUPLICATE KEY UPDATE time = NOW(),isOnline=1");
//        echo "
//			INSERT INTO community_chat_online (com_id,user_id)
//			VALUES (
//				'" . DB::esc($this->com_id) . "',
//				'" . DB::esc($this->user_id) . "'
//		)ON DUPLICATE KEY UPDATE time = NOW(),isOnline=1";
        return DB::getMySQLiObject();
    }

    public function update() {
        DB::query("
			INSERT INTO community_chat_online (com_id,user_id)
			VALUES (
				'" . DB::esc($this->com_id) . "',
				'" . DB::esc($this->user_id) . "'
			) ON DUPLICATE KEY UPDATE time = NOW(),isOnline=1");
    }

}

?>