--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'php';

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 997927)
-- Name: admincomponents; Type: TABLE; Schema: public; Owner: php
--

CREATE TABLE admincomponents (
    componentid serial NOT NULL,
    shortname character varying(255),
    title character varying(255),
    description text,
    displayorder integer DEFAULT 0,
    section integer NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    "show" boolean DEFAULT true NOT NULL
);


--
-- Data for TOC entry 6 (OID 997927)
-- Name: admincomponents; Type: TABLE DATA; Schema: public; Owner: php
--

INSERT INTO admincomponents (componentid, shortname, title, description, displayorder, section, enabled, "show") VALUES (44, 'AdminSubComponents', 'Admin Sub-Components', NULL, 5, 1, true, false);
INSERT INTO admincomponents (componentid, shortname, title, description, displayorder, section, enabled, "show") VALUES (3, 'AdminSections', 'Admin Sections', NULL, 3, 1, true, true);
INSERT INTO admincomponents (componentid, shortname, title, description, displayorder, section, enabled, "show") VALUES (4, 'AdminComponents', 'Admin Components', NULL, 4, 1, true, true);
INSERT INTO admincomponents (componentid, shortname, title, description, displayorder, section, enabled, "show") VALUES (1, 'AdminUsers', 'Admin Users', 'Users Tool', 1, 1, false, true);
INSERT INTO admincomponents (componentid, shortname, title, description, displayorder, section, enabled, "show") VALUES (52, 'AdminGroups', 'Admin Groups', NULL, 2, 1, false, true);


--
-- TOC entry 5 (OID 997935)
-- Name: admincomponents_pkey; Type: CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY admincomponents
    ADD CONSTRAINT admincomponents_pkey PRIMARY KEY (componentid);


--
-- TOC entry 7 (OID 997937)
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY admincomponents
    ADD CONSTRAINT "$1" FOREIGN KEY (section) REFERENCES adminsections(sectionid);


--
-- TOC entry 8 (OID 3280633)
-- Name: tr_delete; Type: TRIGGER; Schema: public; Owner: php
--

CREATE TRIGGER tr_delete
    BEFORE DELETE ON admincomponents
    FOR EACH ROW
    EXECUTE PROCEDURE tr_admincomponents_delete();


--
-- TOC entry 4 (OID 997925)
-- Name: admincomponents_componentid_seq; Type: SEQUENCE SET; Schema: public; Owner: php
--

SELECT pg_catalog.setval('admincomponents_componentid_seq', 73, true);


