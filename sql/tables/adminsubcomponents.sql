--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'php';

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 998902)
-- Name: adminsubcomponents; Type: TABLE; Schema: public; Owner: php
--

CREATE TABLE adminsubcomponents (
    subcomponentid serial NOT NULL,
    component integer NOT NULL,
    title character varying(255),
    shortname character varying(50),
    "show" boolean DEFAULT false NOT NULL,
    displayorder integer DEFAULT 0
);


--
-- Data for TOC entry 6 (OID 998902)
-- Name: adminsubcomponents; Type: TABLE DATA; Schema: public; Owner: php
--

INSERT INTO adminsubcomponents (subcomponentid, component, title, shortname, "show", displayorder) VALUES (8, 1, 'Login History', 'LoginHistory', true, 0);


--
-- TOC entry 5 (OID 998907)
-- Name: pk_adminsubcomponents; Type: CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY adminsubcomponents
    ADD CONSTRAINT pk_adminsubcomponents PRIMARY KEY (subcomponentid);


--
-- TOC entry 7 (OID 998909)
-- Name: fk_adminsubcomponents_admincomponents; Type: FK CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY adminsubcomponents
    ADD CONSTRAINT fk_adminsubcomponents_admincomponents FOREIGN KEY (component) REFERENCES admincomponents(componentid);


--
-- TOC entry 4 (OID 998900)
-- Name: adminsubcomponents_subcomponentid_seq; Type: SEQUENCE SET; Schema: public; Owner: php
--

SELECT pg_catalog.setval('adminsubcomponents_subcomponentid_seq', 9, true);


