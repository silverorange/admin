CREATE OR REPLACE FUNCTION "public"."tr_admingroups_delete" () RETURNS trigger AS ' 
    BEGIN
       
		delete from admincomponent_admingroup where groupnum = OLD.id;
		delete from adminuser_admingroup where groupnum = OLD.id;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_delete BEFORE DELETE ON admingroups
    FOR EACH ROW EXECUTE PROCEDURE tr_admingroups_delete();
