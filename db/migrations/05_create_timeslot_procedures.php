<?php

use Phinx\Migration\AbstractMigration;

class CreateTimeslotProcedures extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE DEFINER=CURRENT_USER PROCEDURE `meb_add_slot` (IN `old_mod_date` TIMESTAMP, IN `event_hash` VARCHAR(255), IN `slot_hash` VARCHAR(255), IN `t_start` DATETIME, IN `t_end` DATETIME, IN `t_duration` INT, IN `cap` INT, OUT `res` INT) BEGIN
                SELECT id, capacity, open_slots, mod_date
                INTO @eid, @ecap, @eslots, @new_mod_date
                from meb_event
                WHERE hash = event_hash;
                IF (old_mod_date = @new_mod_date) THEN
                    BEGIN
                        INSERT INTO meb_timeslot (hash, start_time, end_time, duration, slot_capacity, spaces_available, is_full,
                                              fk_event_id)
                        VALUES (slot_hash, t_start, t_end, t_duration, cap, cap, 0, @eid);
                        UPDATE meb_event
                        SET capacity = @ecap + cap, open_slots = @eslots + cap, mod_date = CURRENT_TIMESTAMP
                        WHERE hash = event_hash;
                        SET res = 0;
                    END;
                ELSE
                    BEGIN
                        SET res = -1;
                    END;
                END IF;
            END
        ");
        $this->execute("
            CREATE DEFINER=CURRENT_USER PROCEDURE `meb_delete_reservation` (IN `slot_hash` VARCHAR(255), IN `user_onid` VARCHAR(255), OUT `res` INT) BEGIN
                SELECT is_full, slot_capacity, spaces_available, fk_event_id, mod_date
                INTO @full, @cap, @sa, @eid, @t_mod_date_old
                FROM meb_timeslot
                WHERE hash = slot_hash;
                SELECT open_slots, mod_date INTO @open_slots, @e_mod_date_old FROM meb_event WHERE id = @eid;
                SELECT mod_date INTO @t_mod_date_new FROM meb_timeslot WHERE hash = slot_hash;
                SELECT mod_date INTO @e_mod_date_new FROM meb_event WHERE id = @eid;
                IF ((@full = 0) && (@t_mod_date_new = @t_mod_date_old)) THEN
                    BEGIN
                        DELETE b
                        FROM meb_booking as b
                                 INNER JOIN meb_timeslot t on t.id = b.fk_timeslot_id
                                 INNER JOIN meb_user u on u.id = b.fk_user_id
                        WHERE t.hash = slot_hash
                          AND u.onid = user_onid;
                        UPDATE meb_timeslot SET spaces_available = @sa + 1, mod_date = CURRENT_TIMESTAMP WHERE hash = slot_hash;
                        UPDATE meb_event SET open_slots = @open_slots + 1, mod_date = CURRENT_TIMESTAMP WHERE id = @eid;
                        SET res = 0;
                    END;
                ELSEIF ((@full = 1) && (@t_mod_date_old = @t_mod_date_new)) THEN
                    BEGIN
                        DELETE b
                        FROM meb_booking as b
                                 INNER JOIN meb_timeslot t on t.id = b.fk_timeslot_id
                                 INNER JOIN meb_user u on u.id = b.fk_user_id
                        WHERE t.hash = slot_hash
                          AND u.onid = user_onid;
                        UPDATE meb_timeslot
                        SET spaces_available = @sa + 1, is_full = 0, mod_date = CURRENT_TIMESTAMP
                        WHERE hash = slot_hash;
                        UPDATE meb_event SET open_slots = @open_slots + 1, mod_date = CURRENT_TIMESTAMP WHERE id = @eid;
                        SET res = 0;
                    END;
                ELSE
                    BEGIN
                        SET res = -1;
                    END;
                END IF;
            END
        ");
        $this->execute("
            CREATE DEFINER=CURRENT_USER PROCEDURE `meb_delete_slot` (IN `old_mod_date` TIMESTAMP, IN `event_hash` VARCHAR(255), IN `slot_hash` VARCHAR(255), OUT `res` INT) BEGIN
                SELECT capacity, open_slots, mod_date INTO @ecap, @eslots, @new_mod_date from meb_event WHERE hash = event_hash;
                SELECT spaces_available, slot_capacity INTO @sa, @scap from meb_timeslot WHERE hash = slot_hash;
                IF (old_mod_date = @new_mod_date) THEN
                    BEGIN
                        DELETE FROM meb_timeslot WHERE hash = slot_hash;
                        UPDATE meb_event SET capacity = @ecap - @scap, open_slots = @eslots - @sa WHERE hash = event_hash;
                        SET res = 0;
                    END;
                ELSE
                    BEGIN
                        SET res = -1;
                    end;
                END IF;
            END
        ");
        $this->execute("
            CREATE DEFINER=CURRENT_USER PROCEDURE `meb_insert_booking` (IN `timeslot_id` INT, IN `user_id` INT, OUT `res` INT) BEGIN
                SET res = 0;
                SELECT is_full, slot_capacity, spaces_available, fk_event_id, mod_date
                INTO @full, @cap, @sa, @eid, @t_mod_date_old
                FROM meb_timeslot
                WHERE id = timeslot_id;
                SELECT open_slots, mod_date INTO @total_space, @e_mod_date_old FROM meb_event WHERE id = @eid;
                SELECT mod_date INTO @t_mod_date_new FROM meb_timeslot WHERE id = timeslot_id;
                SELECT mod_date INTO @e_mod_date_new FROM meb_event WHERE id = @eid;
                IF ((@full = 0) && (@sa - 1) = 0 && (@t_mod_date_old = @t_mod_date_new) && (@e_mod_date_old = @e_mod_date_new)) THEN
                    BEGIN
                        UPDATE meb_timeslot SET is_full = 1, spaces_available = 0, mod_date = CURRENT_TIMESTAMP WHERE id = timeslot_id;
                        INSERT INTO meb_booking (fk_timeslot_id, fk_user_id) VALUES (timeslot_id, user_id);
                        UPDATE meb_event SET open_slots = @total_space - 1, mod_date = CURRENT_TIMESTAMP WHERE id = @eid;
                        SELECT spaces_available INTO @num_spaces FROM meb_timeslot WHERE id = timeslot_id;
                        SET res = @num_spaces;
                    END;
                ELSEIF ((@full = 0) && ((@sa - 1) > 0) && (@t_mod_date_old = @t_mod_date_new) &&
                        (@e_mod_date_old = @e_mod_date_new)) THEN
                    BEGIN
                        UPDATE meb_timeslot SET spaces_available = @sa - 1, mod_date = CURRENT_TIMESTAMP WHERE id = timeslot_id;
                        INSERT INTO meb_booking (fk_timeslot_id, fk_user_id) VALUES (timeslot_id, user_id);
                        UPDATE meb_event SET open_slots = @total_space - 1, mod_date = CURRENT_TIMESTAMP WHERE id = @eid;
                        SELECT spaces_available INTO @num_spaces FROM meb_timeslot WHERE id = timeslot_id;
                        SET res = @num_spaces;
                    END;
                ELSE
                    BEGIN
                        SET res = -1;
                    END;
                END IF;

            END
        ");
        $this->execute("
            CREATE DEFINER=CURRENT_USER PROCEDURE `meb_reserve_slot` (IN `timeslot_id` INT, IN `user_id` INT, OUT `res` INT) BEGIN
                SELECT fk_event_id INTO @event_id FROM meb_timeslot WHERE id = timeslot_id;
                SELECT count(*)
                INTO @num_bookings
                FROM meb_booking
                         INNER JOIN meb_timeslot t ON meb_booking.fk_timeslot_id = t.id
                WHERE t.fk_event_id = @event_id
                  and meb_booking.fk_user_id = user_id;
                IF (@num_bookings > 0) THEN
                    BEGIN
                        SELECT t.id, b.id
                        into @old_slot_id, @booking_id
                        FROM meb_timeslot t
                                 INNER JOIN meb_booking b on t.id = b.fk_timeslot_id
                        WHERE t.fk_event_id = @event_id
                          and b.fk_user_id = user_id;
                        CALL meb_update_booking(timeslot_id, @old_slot_id, @booking_id, user_id, @update_res);
                        SET res = @update_res;
                    END;
                ELSE
                    BEGIN
                        CALL meb_insert_booking(timeslot_id, user_id, @insert_res);
                        SET res = @insert_res;
                    END;
                END IF;
            END
        ");
        $this->execute("
            CREATE DEFINER=CURRENT_USER PROCEDURE `meb_update_booking` (IN `new_timeslot_id` INT, IN `old_timeslot_id` INT, IN `booking_id` INT, IN `user_id` INT, OUT `res` INT) BEGIN
                SET res = 0;
                SELECT is_full, slot_capacity, spaces_available, fk_event_id, mod_date
                INTO @newt_full, @newt_cap, @newt_sa, @eid, @newt_mod_date_old
                FROM meb_timeslot
                WHERE id = new_timeslot_id;
                SELECT is_full, slot_capacity, spaces_available, fk_event_id, mod_date
                INTO @oldt_full, @oldt_cap, @oldt_sa, @eid, @oldt_mod_date_old
                FROM meb_timeslot
                WHERE id = old_timeslot_id;
                SELECT open_slots, mod_date INTO @total_space, @e_mod_date_old FROM meb_event WHERE id = @eid;
                SELECT mod_date INTO @b_mod_date_old FROM meb_booking WHERE id = booking_id;
                SELECT mod_date INTO @newt_mod_date_new FROM meb_timeslot WHERE id = new_timeslot_id;
                SELECT mod_date INTO @oldt_mod_date_new FROM meb_timeslot WHERE id = old_timeslot_id;
                SELECT mod_date INTO @e_mod_date_new FROM meb_event WHERE id = @eid;
                SELECT mod_date INTO @b_mod_date_new FROM meb_booking WHERE id = booking_id;
                IF ((@newt_full = 0) && (@newt_sa - 1) = 0 && (@newt_mod_date_old = @newt_mod_date_new) &&
                    (@e_mod_date_old = @e_mod_date_new) && (@b_mod_date_old = @b_mod_date_new)) THEN
                    BEGIN
                        UPDATE meb_timeslot
                        SET is_full = 0, spaces_available = 1, mod_date = CURRENT_TIMESTAMP
                        WHERE id = old_timeslot_id;
                        UPDATE meb_timeslot
                        SET is_full = 1, spaces_available = 0, mod_date = CURRENT_TIMESTAMP
                        WHERE id = new_timeslot_id;
                        UPDATE meb_booking SET fk_timeslot_id = new_timeslot_id, mod_date = CURRENT_TIMESTAMP WHERE id = booking_id;
                        SELECT spaces_available INTO @num_spaces FROM meb_timeslot WHERE id = new_timeslot_id;
                        SET res = 1;
                    END;
                ELSEIF ((@newt_full = 0) && ((@newt_sa - 1) > 0) && (@newt_mod_date_old = @newt_mod_date_new) &&
                        (@e_mod_date_old = @e_mod_date_new) && (@b_mod_date_old = @b_mod_date_new)) THEN
                    BEGIN
                        UPDATE meb_timeslot
                        SET spaces_available = @newt_sa - 1, mod_date = CURRENT_TIMESTAMP
                        WHERE id = new_timeslot_id;
                        UPDATE meb_timeslot
                        SET spaces_available = @oldt_sa + 1, mod_date = CURRENT_TIMESTAMP
                        WHERE id = old_timeslot_id;
                        UPDATE meb_booking SET fk_timeslot_id = new_timeslot_id, mod_date = CURRENT_TIMESTAMP WHERE id = booking_id;
                        SELECT spaces_available INTO @num_spaces FROM meb_timeslot WHERE id = new_timeslot_id;
                        SET res = @oldt_sa + 1;
                    END;
                ELSE
                    BEGIN
                        SET res = -1;
                    END;
                END IF;

            END
        ");
    }

    public function down()
    {
        $this->execute("DROP PROCEDURE IF EXISTS meb_add_slot");
        $this->execute("DROP PROCEDURE IF EXISTS meb_delete_reservation");
        $this->execute("DROP PROCEDURE IF EXISTS meb_delete_slot");
        $this->execute("DROP PROCEDURE IF EXISTS meb_insert_booking");
        $this->execute("DROP PROCEDURE IF EXISTS meb_reserve_slot");
        $this->execute("DROP PROCEDURE IF EXISTS meb_update_booking");
    }
}
