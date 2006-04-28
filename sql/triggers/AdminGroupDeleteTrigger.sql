CREATE OR REPLACE FUNCTION AdminGroupDelete () RETURNS trigger AS ' 
    BEGIN
       
		delete from AdminComponentAdminGroupBinding where groupnum = OLD.id;
		delete from AdminUserAdminGroupBinding where groupnum = OLD.id;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_delete BEFORE DELETE ON AdminGroup
    FOR EACH ROW EXECUTE PROCEDURE AdminGroupDelete();
