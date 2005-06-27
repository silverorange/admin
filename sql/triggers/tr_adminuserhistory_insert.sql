CREATE OR REPLACE FUNCTION "public"."tr_adminuserhistory_insert" () RETURNS trigger AS ' 
    BEGIN
       
		delete from adminuserhistory where usernum = NEW.usernum
			AND historyid not in (select historyid from adminuserhistory
				where usernum = NEW.usernum order by logindate desc limit 9);
	   
        RETURN NEW;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_insert AFTER INSERT ON adminuserhistory
    FOR EACH ROW EXECUTE PROCEDURE tr_adminuserhistory_insert();
