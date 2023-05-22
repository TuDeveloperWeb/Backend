<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
                CREATE DEFINER=`root`@`localhost` PROCEDURE `bdprueba`.`sp_update_client`(
                    in _id int,
                    in _fname varchar(100),
                    in _sname varchar(100),
                    in _dob date,
                    in _phone varchar(20),
                    in _email varchar(100),
                    in _address varchar(200),
                    in arrayPayment json
                    )
        begin

            declare i int default 0;
                    update clients  set
                        first_name  = _fname,
                        last_name  = _sname,
                        dob = _dob,
                        phone = _phone,
                        email = _email,
                        address = _address
                    where
                        id = _id;

                    while i<JSON_LENGTH(arrayPayment) DO
                            set @tid = JSON_UNQUOTE(JSON_EXTRACT(arrayPayment,CONCAT('$[', i, '].id')));

                    IF @tid is not null then
                        update
                            payments
                        set
                            precio = JSON_UNQUOTE(JSON_EXTRACT(arrayPayment,CONCAT('$[', i, '].amount'))) ,
                            transaction_date = JSON_UNQUOTE(JSON_EXTRACT(arrayPayment,CONCAT('$[', i, '].transaction_date'))) ,
                            updated_at = now()
                        where
                            id = @tid ;

                    end if;

                set i = i+1;
                    end while;

        end
        ";
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $procedure = "DROP PROCEDURE IF EXISTS  sp_update_client";
        DB::unprepared($procedure);
    }
};
