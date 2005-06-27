--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'php';

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 997918)
-- Name: adminsections; Type: TABLE; Schema: public; Owner: php
--

CREATE TABLE adminsections (
    sectionid serial NOT NULL,
    title character varying(255),
    description character varying(255),
    displayorder integer DEFAULT 0,
    "show" boolean DEFAULT true NOT NULL
);


--
-- Data for TOC entry 6 (OID 997918)
-- Name: adminsections; Type: TABLE DATA; Schema: public; Owner: php
--

INSERT INTO adminsections (sectionid, title, description, displayorder, "show") VALUES (11, 'Settings', NULL, 0, true);
INSERT INTO adminsections (sectionid, title, description, displayorder, "show") VALUES (1, 'Advanced', 'This is a section.', 10, true);
INSERT INTO adminsections (sectionid, title, description, displayorder, "show") VALUES (2, 'Site Content', NULL, 0, true);


--
-- TOC entry 5 (OID 997923)
-- Name: adminsections_pkey; Type: CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY adminsections
    ADD CONSTRAINT adminsections_pkey PRIMARY KEY (sectionid);


--
-- TOC entry 4 (OID 997916)
-- Name: adminsections_sectionid_seq; Type: SEQUENCE SET; Schema: public; Owner: php
--

SELECT pg_catalog.setval('adminsections_sectionid_seq', 11, true);


