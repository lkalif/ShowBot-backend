drop table if exists votes;
drop table if exists suggestions;

create table suggestions (
    id integer not null auto_increment,
    server_id varchar(64) not null,
    channel varchar(32) not null,
    digest varchar(40) not null,
    votes int default 0,
    suggestion varchar(255) not null,
    user varchar(32),
    primary key(id)
);

create index digest_index on suggestions(server_id, channel, digest);
create index channel_index on suggestions(server_id, channel);

create table votes (
    suggestion_id integer,
    user_ip varchar(128),
    foreign key (suggestion_id) references suggestions(id) on delete cascade
)