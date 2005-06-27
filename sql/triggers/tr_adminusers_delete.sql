CREATE OR REPLACE FUNCTION "public"."tr_adminusers_delete" () RETURNS trigger AS ' 
    BEGIN
       
	   	delete from adminuserhistory where usernum = OLD.userid;
	   	delete from adminuser_admingroup where usernum = OLD.userid;
	   
        RETURN OLD;
    END;
' LANGUAGE 'plpgsql';


CREATE TRIGGER tr_delete BEFORE DELETE ON adminusers
    FOR EACH ROW EXECUTE PROCEDURE tr_adminusers_delete();
