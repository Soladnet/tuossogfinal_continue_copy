<?php

/* Chat line is used for the chat entries */

class ChatLine extends ChatBase {

    protected $text = '', $user_id = '', $comid = '';

    public function save() {
        DB::query("
			INSERT INTO community_chat (com_id,user_id,text)
			VALUES (
				'" . DB::esc($this->comid) . "',
				'" . DB::esc($this->user_id) . "',
				'" . DB::esc($this->text) . "'
		)");

        // Returns the MySQLi object of the DB class
        return DB::getMySQLiObject();
    }

}

?>