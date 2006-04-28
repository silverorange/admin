CREATE OR REPLACE FUNCTION AdminUserHistoryInsertTrigger () RETURNS trigger AS ' 
    BEGIN
       
		delete from AdminUserHistory where usernum = NEW.usernum
			AND id not in (select id from AdminUserHistory
				where usernum = NEW.usernum order by logindate desc limit 9);
	   
        RETURN NEW;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_insert AFTER INSERT ON AdminUserHistory
    FOR EACH ROW EXECUTE PROCEDURE AdminUserHistoryInsertTrigger();
