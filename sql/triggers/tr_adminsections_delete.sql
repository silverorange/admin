CREATE OR REPLACE FUNCTION "public"."tr_adminsections_delete" () RETURNS trigger AS ' 
    BEGIN
       
		delete from admincomponents where section = OLD.sectionid;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_delete BEFORE DELETE ON adminsections
    FOR EACH ROW EXECUTE PROCEDURE tr_adminsections_delete();
