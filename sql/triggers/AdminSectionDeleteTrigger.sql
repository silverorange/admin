CREATE OR REPLACE FUNCTION AdminSectionDeleteTrigger () RETURNS trigger AS ' 
    BEGIN
       
		delete from AdminComponent where section = OLD.id;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_delete BEFORE DELETE ON AdminSection
    FOR EACH ROW EXECUTE PROCEDURE AdminSectionDeleteTrigger();
