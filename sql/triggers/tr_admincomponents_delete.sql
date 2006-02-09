CREATE OR REPLACE FUNCTION "public"."tr_admincomponents_delete" () RETURNS trigger AS ' 
    BEGIN
       
	   	delete from adminsubcomponents where component = OLD.id;
		delete from admincomponent_admingroup where component = OLD.id;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';

