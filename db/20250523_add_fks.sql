ALTER TABLE `dates`
    DROP FOREIGN KEY `dates_ibfk_1`; 

ALTER TABLE `dates` 
    ADD CONSTRAINT `dates_ibfk_1` 
        FOREIGN KEY (`event_id`) 
        REFERENCES `events`(`event_id`) 
        ON DELETE CASCADE 
        ON UPDATE RESTRICT;

ALTER TABLE `participants`
    ADD CONSTRAINT `participants_ibfk_1`
        FOREIGN KEY (`event_id`) 
        REFERENCES `events`(`event_id`) 
        ON DELETE CASCADE 
        ON UPDATE RESTRICT;

ALTER TABLE `attendances` 
    ADD CONSTRAINT `attendances_ibfk_1` 
        FOREIGN KEY (`date_id`) 
        REFERENCES `dates`(`date_id`) 
        ON DELETE CASCADE 
        ON UPDATE RESTRICT; 

ALTER TABLE `attendances` 
    ADD CONSTRAINT `attendances_ibfk_2` 
    FOREIGN KEY (`participant_id`) 
    REFERENCES `participants`(`participant_id`) 
    ON DELETE CASCADE ON UPDATE RESTRICT;