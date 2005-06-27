create table adminuserhistory (
        historyid serial,
        usernum int not null constraint fk_adminuserhistory_usernum references adminusers(userid),
        logindate timestamp,
        loginagent varchar(255),
        remoteip varchar(15),
        primary key(historyid)
)
