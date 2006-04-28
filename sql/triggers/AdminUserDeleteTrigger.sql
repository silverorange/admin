CREATE OR REPLACE FUNCTION AdminUserDeleteTrigger () RETURNS trigger AS ' 
    BEGIN
       
	   	delete from AdminUserHistory where usernum = OLD.id;
	   	delete from AdminUserAdminGroupBinding where usernum = OLD.id;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_delete BEFORE DELETE ON AdminUser
    FOR EACH ROW EXECUTE PROCEDURE AdminUserDeleteTrigger();
