--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'php';

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 997960)
-- Name: adminuser_admingroup; Type: TABLE; Schema: public; Owner: php
--

CREATE TABLE adminuser_admingroup (
    usernum integer NOT NULL,
    groupnum integer NOT NULL
);


--
-- Data for TOC entry 5 (OID 997960)
-- Name: adminuser_admingroup; Type: TABLE DATA; Schema: public; Owner: php
--

INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (3, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (2, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (4, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (5, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (6, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (7, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (8, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (9, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (10, 1);
INSERT INTO adminuser_admingroup (usernum, groupnum) VALUES (11, 1);


--
-- TOC entry 4 (OID 997962)
-- Name: adminuser_admingroup_pkey; Type: CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY adminuser_admingroup
    ADD CONSTRAINT adminuser_admingroup_pkey PRIMARY KEY (usernum, groupnum);


--
-- TOC entry 6 (OID 997964)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY adminuser_admingroup
    ADD CONSTRAINT "$1" FOREIGN KEY (usernum) REFERENCES adminusers(userid);


--
-- TOC entry 7 (OID 997968)
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY adminuser_admingroup
    ADD CONSTRAINT "$2" FOREIGN KEY (groupnum) REFERENCES admingroups(groupid);


