CREATE OR REPLACE FUNCTION AdminComponentDeleteTrigger () RETURNS trigger AS ' 
    BEGIN
       
	   	delete from AdminSubComponent where component = OLD.id;
		delete from AdminComponentAdminGroupBinding where component = OLD.id;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';

