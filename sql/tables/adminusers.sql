--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET SESSION AUTHORIZATION 'php';

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 997954)
-- Name: adminusers; Type: TABLE; Schema: public; Owner: php
--

CREATE TABLE adminusers (
    userid serial NOT NULL,
    username character varying(50) NOT NULL,
    name character varying(100) NOT NULL,
    "password" character varying(50) NOT NULL,
    enabled boolean DEFAULT true
);


--
-- Data for TOC entry 6 (OID 997954)
-- Name: adminusers; Type: TABLE DATA; Schema: public; Owner: php
--

INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (4, 'nick', 'Nick Burka', 'f12d534540fdc2b0cd8389c4a54ad1fe', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (11, 'nrf', 'Nathan', 'bfc4fd79ea7fdf1240db016e29ae417a', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (3, 'isaac', 'Isaac', 'A4478ADF55CCF937A1C9AA86493D56DF', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (2, 'so', 'me', '57f417b438b12da673e661e998e5bb92', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (5, 'dan', 'Dan', '7497FC213559C07F355432A21C21745A', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (6, 'desroches', 'Stephen DesRoches', 'f1d5981e34c60eb678516587a27bdb53', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (7, 'gauthierm', 'Mike', '9b5ec8bf901d37eaf29c3c775ecab513', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (8, 'dave', 'Dave', 'ae0ce887676b6d4fa6db438c94daca83', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (9, 'steven', 'Steven', '57f417b438b12da673e661e998e5bb92', true);
INSERT INTO adminusers (userid, username, name, "password", enabled) VALUES (10, 'dburka', 'Daniel', '6aecdfe8b004d1e8cb1e42c4414687a9', true);


--
-- TOC entry 5 (OID 997958)
-- Name: adminusers_pkey; Type: CONSTRAINT; Schema: public; Owner: php
--

ALTER TABLE ONLY adminusers
    ADD CONSTRAINT adminusers_pkey PRIMARY KEY (userid);


--
-- TOC entry 4 (OID 997952)
-- Name: adminusers_userid_seq; Type: SEQUENCE SET; Schema: public; Owner: php
--

SELECT pg_catalog.setval('adminusers_userid_seq', 11, true);


