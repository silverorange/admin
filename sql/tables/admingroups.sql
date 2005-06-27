--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'php';

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 997943)
-- Name: admingroups; Type: TABLE; Schema: public; Owner: php
--

CREATE TABLE admingroups (
    groupid serial NOT NULL,
    title character varying(255)
);


--
-- Data for TOC entry 6 (OID 997943)
-- Name: admingroups; Type: TABLE DATA; Schema: public; Owner: php
--

INSERT INTO admingroups (groupid, title) VALUES (1, 'silverorange');


--
-- TOC entry 5 (OID 997946)
-- Name: admingroups_pkey; Type: CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY admingroups
    ADD CONSTRAINT admingroups_pkey PRIMARY KEY (groupid);


--
-- TOC entry 4 (OID 997941)
-- Name: admingroups_groupid_seq; Type: SEQUENCE SET; Schema: public; Owner: php
--

SELECT pg_catalog.setval('admingroups_groupid_seq', 5, true);


