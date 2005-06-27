--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'php';

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 997976)
-- Name: admincomponent_admingroup; Type: TABLE; Schema: public; Owner: php
--

CREATE TABLE admincomponent_admingroup (
    component integer NOT NULL,
    groupnum integer NOT NULL
);


--
-- Data for TOC entry 5 (OID 997976)
-- Name: admincomponent_admingroup; Type: TABLE DATA; Schema: public; Owner: php
--

INSERT INTO admincomponent_admingroup (component, groupnum) VALUES (1, 1);
INSERT INTO admincomponent_admingroup (component, groupnum) VALUES (4, 1);
INSERT INTO admincomponent_admingroup (component, groupnum) VALUES (52, 1);
INSERT INTO admincomponent_admingroup (component, groupnum) VALUES (3, 1);
INSERT INTO admincomponent_admingroup (component, groupnum) VALUES (44, 1);


--
-- TOC entry 4 (OID 997978)
-- Name: admincomponent_admingroup_pkey; Type: CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY admincomponent_admingroup
    ADD CONSTRAINT admincomponent_admingroup_pkey PRIMARY KEY (component, groupnum);


--
-- TOC entry 6 (OID 997980)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY admincomponent_admingroup
    ADD CONSTRAINT "$1" FOREIGN KEY (component) REFERENCES admincomponents(componentid);


--
-- TOC entry 7 (OID 997984)
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY admincomponent_admingroup
    ADD CONSTRAINT "$2" FOREIGN KEY (groupnum) REFERENCES admingroups(groupid);


