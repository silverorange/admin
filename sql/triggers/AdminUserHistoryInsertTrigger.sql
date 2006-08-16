CREATE OR REPLACE FUNCTION updateAdminUserHistory() RETURNS trigger AS ' 
    BEGIN
       
		delete from AdminUserHistory where usernum = NEW.usernum
			AND id not in (select id from AdminUserHistory
				where usernum = NEW.usernum order by login_date desc limit 9);
	   
        RETURN NEW;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER AdminUserHistoryInsertTrigger AFTER INSERT ON AdminUserHistory
    FOR EACH ROW EXECUTE PROCEDURE updateAdminUserHistory();
