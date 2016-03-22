drop table if exists clan_edit_requests;
drop table if exists loot_report_player_result;
drop table if exists loot_report;
drop table if exists proxy_request_count;
drop table if exists war_edit_requests;
drop table if exists war_allowed_users;
drop table if exists api_keys;
drop table if exists clan_allowed_users;
drop table if exists player_allowed_users;
drop table if exists user;
drop table if exists war_attack;
drop table if exists war_player;
drop table if exists war;
drop table if exists clan_member;
drop table if exists clan;
drop table if exists loot;
drop table if exists player;

create table player(
	id int auto_increment not null,
	name varchar(50) not null,
	tag varchar(15) not null unique,
	date_created datetime not null,
	date_modified datetime default null,
	primary key(id)
);

create table clan(
	id int auto_increment not null,
	name varchar(50),
	tag varchar(15) not null unique,
	description varchar(256),
	clan_type varchar(2) not null default 'AN',
	minimum_trophies int not null default 0,
	war_frequency varchar(2) not null default 'NS',
	date_created datetime not null,
	date_modified datetime default null,
	primary key(id)
);

create table clan_member(
	player_id int not null,
	clan_id int not null,
	rank varchar(2) not null,
	date_created datetime not null,
	date_modified datetime default null,
	date_left datetime default null,
	primary key(player_id, clan_id),
	foreign key(player_id) references player(id),
	foreign key(clan_id) references clan(id)
);

create table loot(
	player_id int not null,
	date_recorded datetime not null,
	loot_type varchar(2) not null,
	loot_amount int not null,
	foreign key(player_id) references player(id)
);

create table war(
	id int auto_increment not null,
	first_clan_id int not null,
	second_clan_id int not null,
	size int not null,
	date_created datetime not null,
	date_modified datetime default null,
	primary key(id),
	foreign key(first_clan_id) references clan(id),
	foreign key(second_clan_id) references clan(id)
);

create table war_player(
	war_id int not null,
	player_id int not null,
	clan_id int not null,
	date_created datetime not null,
	primary key(player_id, war_id),
	foreign key(player_id) references player(id),
	foreign key(war_id) references war(id) on delete cascade,
	foreign key(clan_id) references clan(id)
);

create table war_attack(
	war_id int not null,
	attacker_id int not null,
	defender_id int not null,
	attacker_clan_id int not null,
	defender_clan_id int not null,
	stars int not null default 0,
	date_created datetime not null,
	date_modified datetime default null,
	primary key(war_id, attacker_id, defender_id),
	foreign key(war_id, attacker_id) references war_player(war_id, player_id) on delete cascade,
	foreign key(war_id, defender_id) references war_player(war_id, player_id) on delete cascade,
	foreign key(attacker_clan_id) references clan(id) on delete cascade,
	foreign key(defender_clan_id) references clan(id) on delete cascade
);

drop procedure if exists p_player_load;
delimiter //
create procedure p_player_load(varId int)
begin
	select * from player where id = varId;
end //
delimiter ;

drop procedure if exists p_player_load_by_tag;
delimiter //
create procedure p_player_load_by_tag(varTag varchar(15))
begin
	select * from player where tag = varTag;
end //
delimiter ;

drop procedure if exists p_player_set;
delimiter //
create procedure p_player_set(varId int, varKey varchar(40), varValue text)
begin
	set @st := concat('update player set ', varKey, ' = ', quote(varValue), ', date_modified = NOW() where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_player_record_loot;
delimiter // 
create procedure p_player_record_loot(varId int, varType varchar(2), varAmount int, varDate varchar(50))
begin
if(varDate = '%')
	then insert into loot (player_id, date_recorded, loot_type, loot_amount) values (varId, NOW(), varType, varAmount);
	else insert into loot (player_id, date_recorded, loot_type, loot_amount) values (varId, varDate, varType, varAmount);
end if;
end //
delimiter ;

drop procedure if exists p_player_get_loot;
delimiter // 
create procedure p_player_get_loot(varId int, varType varchar(2))
begin
	select * from loot where player_id = varId and loot_type = varType order by date_recorded desc;
end //
delimiter ;

drop procedure if exists p_player_create;
delimiter //
create procedure p_player_create(varName varchar(50), varTag varchar(15))
begin
	insert into player(name, tag, date_created) values(varName, varTag, NOW());
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_clan_create;
delimiter //
create procedure p_clan_create(varName varchar(50), varTag varchar(15), varDescription varchar(256), varType varchar(2), varMinimumTrophies int, varWarFrequency varchar(2))
begin
	insert into clan(name, tag, description, clan_type, minimum_trophies, war_frequency, date_created) values(varName, varTag, varDescription, varType, varMinimumTrophies, varWarFrequency, NOW());
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_clan_load;
delimiter //
create procedure p_clan_load(varId int)
begin
	select * from clan where id = varId;
end //
delimiter ;

drop procedure if exists p_clan_load_by_tag;
delimiter //
create procedure p_clan_load_by_tag(varTag varchar(15))
begin
	select * from clan where tag = varTag;
end //
delimiter ;

drop procedure if exists p_clan_set;
delimiter //
create procedure p_clan_set(varId int, varKey varchar(40), varValue text)
begin
	set @st := concat('update clan set ', varKey, ' = ', quote(varValue), ', date_modified = NOW() where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_clan_add_player;
delimiter //
create procedure p_clan_add_player(varClanId int, varPlayerId int, varRank varchar(2))
begin
if exists (select * from clan_member where clan_id = varClanId and player_id = varPlayerId)
	then 
	if (varRank = 'KI' or varRank = 'EX')
	    then update clan_member set rank = varRank, date_modified = NOW(), date_left = NOW() where clan_id = varClanId and player_id = varPlayerId;
	    else update clan_member set rank = varRank, date_modified = NOW() where clan_id = varClanId and player_id = varPlayerId;
	end if;
	else insert into clan_member(clan_id, player_id, rank, date_created) values(varClanId, varPlayerId, varRank, NOW());
end if;
end //
delimiter ;

drop procedure if exists p_player_get_clan;
delimiter //
create procedure p_player_get_clan(varPlayerId int)
begin
	select clan_id from clan_member where player_id = varPlayerId and rank != 'KI' and rank != 'EX';
end //
delimiter ;

drop procedure if exists p_clan_get_leader;
delimiter //
create procedure p_clan_get_leader(varClanId int)
begin
	select player_id from clan_member where clan_id = varClanId and rank = 'LE';
end //
delimiter ;

drop procedure if exists p_player_leave_clan;
delimiter //
create procedure p_player_leave_clan(varPlayerId int)
begin
	update clan_member set rank = 'EX', date_modified = NOW(), date_left = NOW() where player_id = varPlayerId and rank != 'KI' and rank != 'EX';
end //
delimiter ;

drop procedure if exists p_player_get_rank;
delimiter //
create procedure p_player_get_rank(varPlayerId int, varClanId int)
begin
	select rank from clan_member where player_id = varPlayerId and clan_id = varClanId;
end //
delimiter ;

drop procedure if exists p_clan_get_members;
delimiter //
create procedure p_clan_get_members(varClanId int, varRank varchar(2))
begin
if (varRank = '%')
	then select player_id, name from clan_member join player on player_id = player.id where clan_id = varClanId order by name;
	else select player_id from clan_member join player on player_id = player.id where clan_id = varClanId and rank = varRank order by name;
end if;
end //
delimiter ;

drop procedure if exists p_player_get_clans;
delimiter //
create procedure p_player_get_clans(varPlayerId int)
begin
	select clan_id from clan_member where player_id = varPlayerId order by date_modified;
end //
delimiter ;

drop procedure if exists p_war_create;
delimiter //
create procedure p_war_create(varFirstClanId int, varSecondClanId int, varSize int)
begin
	insert into war(first_clan_id, second_clan_id, size, date_created) values(varFirstClanId, varSecondClanId, varSize, NOW());
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_war_load;
delimiter //
create procedure p_war_load(varId int)
begin
	select * from war where id = varId;
end //
delimiter ;

drop procedure if exists p_clan_get_wars;
delimiter //
create procedure p_clan_get_wars(varClanId int)
begin
	select id from war where first_clan_id = varClanId or second_clan_id = varClanId order by date_created desc;
end //
delimiter ;

drop procedure if exists p_war_add_player;
delimiter //
create procedure p_war_add_player(varWarId int, varPlayerId int, varClanId int)
begin
	insert into war_player(war_id, player_id, clan_id, date_created) values(varWarId, varPlayerId, varClanId, NOW());
	update war set date_modified = NOW() where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_remove_player;
delimiter //
create procedure p_war_remove_player(varWarId int, varPlayerId int)
begin
	delete from war_player where war_id = varWarId and player_id = varPlayerId;
	update war set date_modified = NOW() where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_get_players;
delimiter //
create procedure p_war_get_players(varWarId int, varClanId int)
begin
if (varClanId = '%')
	then select player_id from war_player where war_id = varWarId order by rank;
	else select player_id from war_player where war_id = varWarId and clan_id = varClanId order by rank;
end if;
end //
delimiter ;

drop procedure if exists p_war_add_attack;
delimiter //
create procedure p_war_add_attack(varWarId int, varAttackerId int, varDefenderId int, varAttackerClanId int, varDefenderClanId int, varStars int)
begin
if exists (select * from war_attack where war_id = varWarId and attacker_id = varAttackerId and defender_id = varDefenderId)
	then update war_attack set stars = varStars, date_modified = NOW() where war_id = varWarId and attacker_id = varAttackerId and defender_id = varDefenderId;
	else insert into war_attack(war_id, attacker_id, defender_id, attacker_clan_id, defender_clan_id, stars, date_created) values (varWarId, varAttackerId, varDefenderId, varAttackerClanId, varDefenderClanId, varStars, NOW());
end if;
update war set date_modified = NOW() where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_remove_attack;
delimiter //
create procedure p_war_remove_attack(varWarId int, varAttackerId int, varDefenderId int)
begin
	delete from war_attack where war_id = varWarId and attacker_id = varAttackerId and defender_id = varDefenderId;
	update war set date_modified = NOW() where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_get_attacks;
delimiter //
create procedure p_war_get_attacks(varWarId int, varClanId int)
begin
if (varClanId != '%')
	then select * from war_attack where war_id = varWarId and attacker_clan_id = varClanId order by date_created;
	else select * from war_attack where war_id = varWarId order by date_created;
end if;
end //
delimiter ;

drop procedure if exists p_get_clans;
delimiter //
create procedure p_get_clans(varPageSize int)
begin
	select id from clan order by clan_points desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_get_players;
delimiter //
create procedure p_get_players(varPageSize int)
begin
	select id from player order by trophies desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_get_wars;
delimiter //
create procedure p_get_wars(varPageSize int)
begin
	select id from war order by date_created desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_war_set;
delimiter //
create procedure p_war_set(varId int, varKey varchar(40), varValue text)
begin
	set @st := concat('update war set ', varKey, ' = ', quote(varValue), ', date_modified = NOW() where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_war_get_player_attacks;
delimiter //
create procedure p_war_get_player_attacks(varWarId int, varAttackerId int)
begin
	select * from war_attack where war_id = varWarId and attacker_id = varAttackerId order by date_created;
end //
delimiter ;

drop procedure if exists p_war_get_player_defences;
delimiter //
create procedure p_war_get_player_defences(varWarId int, varDefenderId int)
begin
	select * from war_attack where war_id = varWarId and defender_id = varDefenderId order by date_created;
end //
delimiter ;

drop procedure if exists p_war_get_player_war_clan;
delimiter //
create procedure p_war_get_player_war_clan(varWarId int, varPlayerId int)
begin
	select clan_id from war_player where war_id = varWarId and player_id = varPlayerId;
end //
delimiter ;

drop procedure if exists p_war_get_attack;
delimiter //
create procedure p_war_get_attack(varWarId int, varAttackerId int, varDefenderId int)
begin
	select * from war_attack where war_id = varWarId and attacker_id = varAttackerId and defender_id = varDefenderId;
end //
delimiter ;

alter table clan_member add war_rank int not null;

drop procedure if exists p_clan_update_player_war_rank;
delimiter //
create procedure p_clan_update_player_war_rank(varClanId int, varPlayerId int, varWarRank int)
begin
	update clan_member set war_rank = varWarRank where clan_id = varClanId and player_id = varPlayerId;
end //
delimiter ;

drop procedure if exists p_player_get_war_rank;
delimiter //
create procedure p_player_get_war_rank(varPlayerId int, varClanId int)
begin
	select war_rank from clan_member where player_id = varPlayerId and clan_id = varClanId;
end //
delimiter ;

drop procedure if exists p_clan_get_highest_war_rank;
delimiter //
create procedure p_clan_get_highest_war_rank(varClanId int)
begin 
	select war_rank from clan_member where clan_id = varClanId order by war_rank desc limit 1;
end //
delimiter ;

alter table war_player add rank int not null;

drop procedure if exists p_war_update_player_rank;
delimiter //
create procedure p_war_update_player_rank(varWarId int, varPlayerId int, varRank int)
begin
	update war_player set rank = varRank where war_id = varWarId and player_id = varPlayerId;
	update war set date_modified = NOW() where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_get_highest_rank;
delimiter //
create procedure p_war_get_highest_rank(varWarId int)
begin 
	select rank from war_player where war_id = varWarId order by rank desc limit 1;
end //
delimiter ;

drop procedure if exists p_war_get_player_rank;
delimiter //
create procedure p_war_get_player_rank(varWarId int, varPlayerId int)
begin
	select rank from war_player where player_id = varPlayerId and war_id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_get_player_by_rank;
delimiter //
create procedure p_war_get_player_by_rank(varWarId int, varClanId int, varRank int)
begin
	select player_id from war_player where war_id = varWarId and clan_id = varClanId and rank = varRank;
end //
delimiter ;

drop procedure if exists p_player_get_attacks;
delimiter //
create procedure p_player_get_attacks(varPlayerId int)
begin
	select * from war_attack where attacker_id = varPlayerId;
end //
delimiter ;

drop procedure if exists p_player_get_defences;
delimiter //
create procedure p_player_get_defences(varPlayerId int)
begin
	select * from war_attack where defender_id = varPlayerId;
end //
delimiter ;

drop procedure if exists p_player_get_wars;
delimiter //
create procedure p_player_get_wars(varPlayerId int)
begin
	select war_id from war_player where player_id = varPlayerId order by date_created desc;
end //
delimiter ;

drop procedure if exists p_clan_player_joined;
delimiter //
create procedure p_clan_player_joined(varClanId int, varPlayerId int)
begin
	select date_created as date_joined from clan_member where clan_id = varClanId and player_id = varPlayerId;
end //
delimiter ;

drop procedure if exists p_clan_search;
delimiter //
create procedure p_clan_search(varQuery varChar(50))
begin
	select id from clan where lower(name) like lower(varQuery) or lower(tag) like lower(varQuery) limit 50;
end //
delimiter ;

drop procedure if exists p_player_search;
delimiter //
create procedure p_player_search(varQuery varChar(50))
begin
	select id from player where lower(name) like lower(varQuery) or lower(tag) like lower(varQuery) limit 50;
end //
delimiter ;

drop procedure if exists p_get_players_with_name;
delimiter //
create procedure p_get_players_with_name(varName varChar(50))
begin
	select id from player where name = varName;
end //
delimiter ;

drop procedure if exists p_player_remove_loot;
delimiter //
create procedure p_player_remove_loot(varId int, varType varChar(2), varDate varChar(50))
begin
if(varDate ='%')
	then delete from loot where player_id = varId and loot_type = varType;
	else delete from loot where player_id = varId and loot_type = varType and date_recorded >= varDate;
end if;
end //
delimiter ;

-- New Table and Procedures for user accounts

create table user(
	id int auto_increment not null,
	email varchar(254) not null unique,
	password varchar(255) not null,
	player_id int default null unique,
	date_created datetime not null,
	date_modified datetime default null,
	primary key(id),
	foreign key(player_id) references player(id)
);

drop procedure if exists p_user_create;
delimiter //
create procedure p_user_create(varEmail varchar(254), varPassword varchar(255))
begin
	insert into user(email, password, date_created) values(varEmail, varPassword, NOW());
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_user_load;
delimiter //
create procedure p_user_load(varId int)
begin
	select * from user where id = varId;
end //
delimiter ;

drop procedure if exists p_user_load_by_email;
delimiter //
create procedure p_user_load_by_email(varEmail varchar(254))
begin
	select * from user where email = varEmail;
end //
delimiter ;

drop procedure if exists p_user_change_password;
delimiter //
create procedure p_user_change_password(varId int, varPassword varchar(255))
begin
	update user set password = varPassword, date_modified = NOW() where id = varId;
end //
delimiter ;

drop procedure if exists p_user_link_player;
delimiter //
create procedure p_user_link_player(varUserId int, varPlayerId int)
begin
	update user set player_id = varPlayerId, date_modified = NOW() where id = varUserId;
end //
delimiter ;

drop procedure if exists p_user_unlink_player;
delimiter //
create procedure p_user_unlink_player(varUserId int)
begin
	update user set player_id = null, date_modified = NOW() where id = varUserId;
end //
delimiter ;

drop procedure if exists p_player_get_linked_user;
delimiter //
create procedure p_player_get_linked_user(varId int)
begin
	select id from user where player_id = varId;
end //
delimiter ;

drop procedure if exists p_user_set;
delimiter //
create procedure p_user_set(varId int, varKey varchar(40), varValue text)
begin
	set @st := concat('update user set ', varKey, ' = ', quote(varValue), ', date_modified = NOW() where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

alter table player add access_type varchar(2) not null default 'AN';
alter table player add min_rank_access varchar(2) default null;

create table player_allowed_users(
	player_id int not null,
	user_id int not null,
	primary key(player_id, user_id),
	foreign key(player_id) references player(id),
	foreign key(user_id) references user(id)
);

drop procedure if exists p_player_allow_user;
delimiter //
create procedure p_player_allow_user(varPlayerId int, varUserId int)
begin
	insert into player_allowed_users(player_id, user_id) values(varPlayerId, varUserId);
end //
delimiter ;

drop procedure if exists p_player_disallow_user;
delimiter //
create procedure p_player_disallow_user(varPlayerId int, varUserId int)
begin
	delete from player_allowed_users where player_id = varPlayerId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_player_disallow_all_users;
delimiter //
create procedure p_player_disallow_all_users(varPlayerId int)
begin
	delete from player_allowed_users where player_id = varPlayerId;
end //
delimiter ;

drop procedure if exists p_player_get_allowed_users;
delimiter //
create procedure p_player_get_allowed_users(varPlayerId int)
begin
	select user_id from player_allowed_users where player_id = varPlayerId;
end //
delimiter ;

alter table user add clan_id int unique default null;
alter table clan add access_type varchar(2) not null default 'AN';
alter table clan add min_rank_access varchar(2) default null;

drop procedure if exists p_user_link_clan;
delimiter //
create procedure p_user_link_clan(varUserId int, varClanId int)
begin
	update user set clan_id = varClanId, date_modified = NOW() where id = varUserId;
end //
delimiter ;

drop procedure if exists p_user_unlink_clan;
delimiter //
create procedure p_user_unlink_clan(varUserId int)
begin
	update user set clan_id = null, date_modified = NOW() where id = varUserId;
end //
delimiter ;

drop procedure if exists p_clan_get_linked_user;
delimiter //
create procedure p_clan_get_linked_user(varId int)
begin
	select id from user where clan_id = varId;
end //
delimiter ;

create table clan_allowed_users(
	clan_id int not null,
	user_id int not null,
	primary key(clan_id, user_id),
	foreign key(clan_id) references clan(id) on delete cascade,
	foreign key(user_id) references user(id) on delete cascade
);

drop procedure if exists p_clan_allow_user;
delimiter //
create procedure p_clan_allow_user(varClanId int, varUserId int)
begin
	insert into clan_allowed_users(clan_id, user_id) values(varClanId, varUserId);
end //
delimiter ;

drop procedure if exists p_clan_disallow_user;
delimiter //
create procedure p_clan_disallow_user(varClanId int, varUserId int)
begin
	delete from clan_allowed_users where clan_id = varClanId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_clan_disallow_all_users;
delimiter //
create procedure p_clan_disallow_all_users(varClanId int)
begin
	delete from clan_allowed_users where clan_id = varClanId;
end //
delimiter ;

drop procedure if exists p_clan_get_allowed_users;
delimiter //
create procedure p_clan_get_allowed_users(varClanId int)
begin
	select user_id from clan_allowed_users where clan_id = varClanId;
end //
delimiter ;

create table api_keys(
	ip varchar(39) not null unique,
	api_key varchar(750) not null
);

drop procedure if exists p_api_key_create;
delimiter //
create procedure p_api_key_create(varIp varchar(39), varKey varchar(767))
begin
	insert into api_keys(ip, api_key) values(varIp, varKey);
	select * from api_keys where ip = varIp;
end //
delimiter ;

drop procedure if exists p_api_key_get;
delimiter //
create procedure p_api_key_get(varIp varchar(39))
begin
	select * from api_keys where ip = varIp;
end //
delimiter ;

alter table clan add members int default 0;
alter table clan add clan_level int default 1;
alter table clan add clan_points int default 0;
alter table clan add war_wins int default 0;
alter table clan add badgeUrl varchar(200) default null;

alter table player add level int default 1;
alter table player add trophies int default 0;
alter table player add donations int default 0;
alter table player add received int default 0;

drop procedure if exists p_clan_delete;
delimiter //
create procedure p_clan_delete(varId int)
begin
	delete from clan where id = varId;
end //
delimiter ;

drop procedure if exists p_api_delete;
delimiter //
create procedure p_api_delete(varIp varchar(39))
begin
	delete from api_keys where ip = varIp;
end //
delimiter ;

alter table player add league_url varchar(200) default null;
alter table clan add location varchar(50) default null;

drop table if exists war_allowed_users;
create table war_allowed_users(
	war_id int not null,
	user_id int not null,
	primary key(war_id, user_id),
	foreign key(war_id) references war(id) on delete cascade,
	foreign key(user_id) references user(id) on delete cascade
);

drop procedure if exists p_war_allow_user;
delimiter //
create procedure p_war_allow_user(varWarId int, varUserId int)
begin
	insert into war_allowed_users(war_id, user_id) values(varWarId, varUserId);
end //
delimiter ;

drop procedure if exists p_war_disallow_user;
delimiter //
create procedure p_war_disallow_user(varWarId int, varUserId int)
begin
	delete from war_allowed_users where war_id = varWarId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_war_disallow_all_users;
delimiter //
create procedure p_war_disallow_all_users(varWarId int)
begin
	delete from war_allowed_users where war_id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_get_allowed_users;
delimiter //
create procedure p_war_get_allowed_users(varWarId int)
begin
	select user_id from war_allowed_users where war_id = varWarId;
end //
delimiter ;

drop table if exists war_edit_requests;
create table war_edit_requests(
	war_id int not null,
	user_id int not null,
	message varchar(256),
	primary key(war_id, user_id),
	foreign key(war_id) references war(id) on delete cascade,
	foreign key(user_id) references user(id) on delete cascade
);

drop procedure if exists p_war_edit_request_create;
delimiter //
create procedure p_war_edit_request_create(varWarId int, varUserId int, varMessage varchar(256))
begin
	insert into war_edit_requests values(varWarId, varUserId, varMessage);
end //
delimiter ;

drop procedure if exists p_war_edit_request_delete;
delimiter //
create procedure p_war_edit_request_delete(varWarId int, varUserId int)
begin
	delete from war_edit_requests where war_id = varWarId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_war_get_edit_requests;
delimiter //
create procedure p_war_get_edit_requests(varWarId int)
begin
	select user.*, war_edit_requests.message from user join war_edit_requests on id = user_id where war_id = varWarId;
end //
delimiter ;

drop procedure if exists p_get_players;
delimiter //
create procedure p_get_players(varPageSize int)
begin
	select * from player order by trophies desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_player_get_clan;
delimiter //
create procedure p_player_get_clan(varPlayerId int)
begin
	select clan.*, rank from clan_member join clan on clan.id = clan_id where player_id = varPlayerId and rank != 'KI' and rank != 'EX';
end //
delimiter ;

drop procedure if exists p_get_clans;
delimiter //
create procedure p_get_clans(varPageSize int)
begin
	select * from clan order by clan_points desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_clan_get_past_and_current_members;
delimiter //
create procedure p_clan_get_past_and_current_members(varClanId int)
begin
	select player.* from clan_member join player on player_id = player.id where clan_id = varClanId order by trophies desc;
end //
delimiter ;

drop procedure if exists p_clan_get_current_members;
delimiter //
create procedure p_clan_get_current_members(varClanId int)
begin
	select player.*, rank from clan_member join player on player_id = player.id where clan_id = varClanId and rank != 'KI' and rank != 'EX' order by trophies desc;
end //
delimiter ;

drop procedure if exists p_clan_get_wars;
delimiter //
create procedure p_clan_get_wars(varClanId int)
begin
	select * from war where first_clan_id = varClanId or second_clan_id = varClanId order by date_created desc;
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50))
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation where id = varId;
end //
delimiter ;

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200))
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 'KI' and rank != 'EX';
end //
delimiter ;

drop procedure if exists p_player_get_clans;
delimiter //
create procedure p_player_get_clans(varPlayerId int)
begin
	select * from clan where id in (select clan_id from clan_member where player_id = varPlayerId order by date_modified);
end //
delimiter ;

drop procedure if exists p_war_get_players;
delimiter //
create procedure p_war_get_players(varWarId int, varClanId int)
begin
if (varClanId = '%')
	then select player.* from player join war_player on war_player.player_id = player.id where war_player.war_id = varWarId order by war_player.rank;
	else select player.* from player join war_player on war_player.player_id = player.id where war_player.war_id = varWarId and clan_id = varClanId order by war_player.rank;
end if;
end //
delimiter ;

drop procedure if exists p_get_wars;
delimiter //
create procedure p_get_wars(varPageSize int)
begin
	select * from war order by date_created desc limit varPageSize;
end //
delimiter ;

alter table war add first_clan_stars int default 0;
alter table war add second_clan_stars int default 0;

drop procedure if exists p_player_get_wars;
delimiter //
create procedure p_player_get_wars(varPlayerId int)
begin
	select war.* from war_player join war on war.id = war_player.war_id where player_id = varPlayerId order by date_created desc;
end //
delimiter ;

create table proxy_request_count(
	count int default 0,
	month varchar(10) not null,
	monthly_limit int not null,
	env varchar(200) not null,
	ip varchar(39) not null,
	primary key(env)
);

drop procedure if exists p_proxy_request_get;
delimiter //
create procedure p_proxy_request_get()
begin
	select * from proxy_request_count order by count / monthly_limit;
end //
delimiter ;

drop procedure if exists p_proxy_request_count_update;
delimiter //
create procedure p_proxy_request_count_update(varEnv varchar(200), varCount int, varMonth varchar(10))
begin
	update proxy_request_count set count = varCount, month = varMonth where env = varEnv;
end //
delimiter ;

drop procedure if exists p_clan_search;
delimiter //
create procedure p_clan_search(varQuery varChar(50))
begin
	select * from clan where lower(name) like lower(varQuery) or lower(tag) like lower(varQuery) limit 50;
end //
delimiter ;

drop procedure if exists p_player_search;
delimiter //
create procedure p_player_search(varQuery varChar(50))
begin
	select * from player where lower(name) like lower(varQuery) or lower(tag) like lower(varQuery) limit 50;
end //
delimiter ;

drop procedure if exists p_player_get_loot;
delimiter // 
create procedure p_player_get_loot(varId int)
begin
	select * from loot where player_id = varId order by date_recorded desc;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from loot join player on player.id = player_id where date_recorded > varDate and loot_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 'EX' and rank != 'KI') group by player_id having count(*) > 1;
end //
delimiter ;

create table loot_report(
	id int auto_increment not null,
	clan_id int not null,
	date_created datetime not null,
	primary key(id),
	foreign key(clan_id) references clan(id)
);

create table loot_report_player_result(
	player_id int not null,
	loot_report_id int not null,
	loot_type varchar(2) not null,
	loot_amount int not null,
	foreign key(player_id) references player(id) on delete cascade,
	foreign key(loot_report_id) references loot_report(id) on delete cascade
);

drop procedure if exists p_loot_report_create;
delimiter //
create procedure p_loot_report_create(varClanId int)
begin
	insert into loot_report(clan_id, date_created) values(varClanId, NOW());
	select * from loot_report where id in (select last_insert_id() as id);
end //
delimiter ;

drop procedure if exists p_loot_report_record_player_result;
delimiter //
create procedure p_loot_report_record_player_result(varPlayerId int, varLootReportId int, varLootType varchar(2), varLootAmount int)
begin
	insert into loot_report_player_result values(varPlayerId, varLootReportId, varLootType, varLootAmount);
end //
delimiter ;

drop procedure if exists p_loot_report_delete;
delimiter //
create procedure p_loot_report_delete(varLootReportId int)
begin
	delete from loot_report where id = varLootReportId;
end //
delimiter ;

drop procedure if exists p_loot_report_load;
delimiter //
create procedure p_loot_report_load(varLootReportId int)
begin
	select * from loot_report where id = varLootReportId;
end //
delimiter ;

drop procedure if exists p_loot_report_get_results;
delimiter //
create procedure p_loot_report_get_results(varLootReportId int)
begin
	select * from player join loot_report_player_result on player.id = player_id where loot_report_id = varLootReportId order by loot_amount desc;
end //
delimiter ;

drop procedure if exists p_clan_get_loot_reports;
delimiter //
create procedure p_clan_get_loot_reports(varClanId int)
begin
	select * from loot_report where clan_id = varClanId order by date_created desc;
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50))
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation where id = varId;
end //
delimiter ;

drop procedure if exists p_clan_update_player_war_rank;
delimiter //
create procedure p_clan_update_player_war_rank(varClanId int, varPlayerId int, varWarRank int)
begin
	update clan_member set war_rank = varWarRank, date_modifed = NOW() where clan_id = varClanId and player_id = varPlayerId;
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified where id = varId;
end //
delimiter ;

drop procedure if exists p_clan_update_player_war_rank;
delimiter //
create procedure p_clan_update_player_war_rank(varClanId int, varPlayerId int, varWarRank int, varDateModified datetime)
begin
	update clan_member set war_rank = varWarRank, date_modified = varDateModified where clan_id = varClanId and player_id = varPlayerId;
end //
delimiter ;

alter table clan add api_info varchar(50000) default null;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varApiInfo varchar(50000))
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified, api_info=varApiInfo where id = varId;
end //
delimiter ;

create table clan_edit_requests(
	clan_id int not null,
	user_id int not null,
	message varchar(256),
	primary key(clan_id, user_id),
	foreign key(clan_id) references clan(id) on delete cascade,
	foreign key(user_id) references user(id) on delete cascade
);

drop procedure if exists p_clan_edit_request_create;
delimiter //
create procedure p_clan_edit_request_create(varClanId int, varUserId int, varMessage varchar(256))
begin
	insert into clan_edit_requests values(varClanId, varUserId, varMessage);
end //
delimiter ;

drop procedure if exists p_clan_edit_request_delete;
delimiter //
create procedure p_clan_edit_request_delete(varClanId int, varUserId int)
begin
	delete from clan_edit_requests where clan_id = varClanId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_clan_get_edit_requests;
delimiter //
create procedure p_clan_get_edit_requests(varClanId int)
begin
	select user.*, clan_edit_requests.message from user join clan_edit_requests on id = user_id where clan_edit_requests.clan_id = varClanId;
end //
delimiter ;

drop procedure if exists p_get_api_keys;
delimiter //
create procedure p_get_api_keys()
begin
	select * from api_keys order by ip;
end //
delimiter ;

rename table loot to player_stats;
alter table player_stats change loot_type stat_type varchar(2);
alter table player_stats change loot_amount stat_amount int;

drop procedure if exists p_player_record_loot;
delimiter // 
create procedure p_player_record_loot(varId int, varType varchar(2), varAmount int, varDate varchar(50))
begin
if(varDate = '%')
	then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, NOW(), varType, varAmount);
	else insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, varType, varAmount);
end if;
end //
delimiter ;

drop procedure if exists p_player_remove_loot;
delimiter //
create procedure p_player_remove_loot(varId int, varType varChar(2), varDate varChar(50))
begin
if(varDate ='%')
	then delete from player_stats where player_id = varId and stat_type = varType;
	else delete from player_stats where player_id = varId and stat_type = varType and date_recorded >= varDate;
end if;
end //
delimiter ;

drop procedure if exists p_player_get_loot;
delimiter // 
create procedure p_player_get_loot(varId int)
begin
	select * from player_stats where player_id = varId order by date_recorded desc;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from player_stats join player on player.id = player_id where date_recorded > varDate and stat_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 'EX' and rank != 'KI') group by player_id having count(*) > 1;
end //
delimiter ;

create table clan_stats(
	clan_id int not null,
	date_recorded datetime not null,
	stat_type varchar(2) not null,
	stat_amount int not null,
	foreign key(clan_id) references clan(id)
);

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200), varDate datetime)
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 'KI' and rank != 'EX';
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'LV', varLevel);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'TR', varTrophies);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'DO', varDonations);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'RE', varReceived);
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varApiInfo varchar(50000), varDate datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified, api_info=varApiInfo where id = varId;
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'CP', varClanPoints);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'CL', varClanLevel);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'ME', varMembers);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'WW', varWarWins);
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varApiInfo varchar(50000), varHourAgo datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified, api_info=varApiInfo where id = varId;
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CP', varClanPoints);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CL', varClanLevel);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'ME', varMembers);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'WW', varWarWins);
	update clan set api_info = null where api_info is not null and date_modified < varHourAgo;
end //
delimiter ;

drop procedure if exists p_loot_report_create;
delimiter //
create procedure p_loot_report_create(varClanId int, varDate datetime)
begin
	insert into loot_report(clan_id, date_created) values(varClanId, varDate);
	select * from loot_report where id in (select last_insert_id() as id);
end //
delimiter ;

alter table loot_report_player_result add primary key (player_id, loot_report_id, loot_type);

drop procedure if exists p_player_best_report_results;
delimiter //
create procedure p_player_best_report_results(varPlayerId int)
begin
	select loot_type, max(loot_amount) as max from loot_report_player_result where player_id = varPlayerId group by loot_type;
end //
delimiter ;

alter table clan_member change rank rank_temp varchar(2);
alter table clan_member add column rank int;
update clan_member set rank = 1 where rank_temp = 'LE';
update clan_member set rank = 2 where rank_temp = 'CO';
update clan_member set rank = 3 where rank_temp = 'EL';
update clan_member set rank = 4 where rank_temp = 'ME';
update clan_member set rank = 5 where rank_temp = 'EX';
update clan_member set rank = 5 where rank_temp = 'KI';
alter table clan_member drop column rank_temp;

alter table clan change min_rank_access min_rank_access_temp varchar(2);
alter table clan add column min_rank_access int;
update clan set min_rank_access = 1 where min_rank_access_temp = 'LE';
update clan set min_rank_access = 2 where min_rank_access_temp = 'CO';
update clan set min_rank_access = 3 where min_rank_access_temp = 'EL';
update clan set min_rank_access = 4 where min_rank_access_temp = 'ME';
update clan set min_rank_access = 5 where min_rank_access_temp = 'EX';
update clan set min_rank_access = 5 where min_rank_access_temp = 'KI';
alter table clan drop column min_rank_access_temp;

alter table player change min_rank_access min_rank_access_temp varchar(2);
alter table player add column min_rank_access int;
update player set min_rank_access = 1 where min_rank_access_temp = 'LE';
update player set min_rank_access = 2 where min_rank_access_temp = 'CO';
update player set min_rank_access = 3 where min_rank_access_temp = 'EL';
update player set min_rank_access = 4 where min_rank_access_temp = 'ME';
update player set min_rank_access = 5 where min_rank_access_temp = 'EX';
update player set min_rank_access = 5 where min_rank_access_temp = 'KI';
alter table player drop column min_rank_access_temp;

drop procedure if exists p_clan_get_leader;
delimiter //
create procedure p_clan_get_leader(varClanId int)
begin
	select player_id from clan_member where clan_id = varClanId and rank = 1;
end //
delimiter ;

drop procedure if exists p_clan_add_player;
delimiter //
create procedure p_clan_add_player(varClanId int, varPlayerId int, varRank varchar(2))
begin
if exists (select * from clan_member where clan_id = varClanId and player_id = varPlayerId)
	then 
	if (varRank = 5)
	    then update clan_member set rank = varRank, date_modified = NOW(), date_left = NOW() where clan_id = varClanId and player_id = varPlayerId;
	    else update clan_member set rank = varRank, date_modified = NOW() where clan_id = varClanId and player_id = varPlayerId;
	end if;
	else insert into clan_member(clan_id, player_id, rank, date_created) values(varClanId, varPlayerId, varRank, NOW());
end if;
end //
delimiter ;

drop procedure if exists p_player_get_clan;
delimiter //
create procedure p_player_get_clan(varPlayerId int)
begin
	select clan_id from clan_member where player_id = varPlayerId and rank != 5;
end //
delimiter ;

drop procedure if exists p_player_leave_clan;
delimiter //
create procedure p_player_leave_clan(varPlayerId int)
begin
	update clan_member set rank = 'EX', date_modified = NOW(), date_left = NOW() where player_id = varPlayerId and rank != 5;
end //
delimiter ;

drop procedure if exists p_player_get_clan;
delimiter //
create procedure p_player_get_clan(varPlayerId int)
begin
	select clan.*, rank from clan_member join clan on clan.id = clan_id where player_id = varPlayerId and rank != 5;
end //
delimiter ;

drop procedure if exists p_clan_get_current_members;
delimiter //
create procedure p_clan_get_current_members(varClanId int, varOrder varchar(50))
begin
	set @st := concat('select player.*, rank from clan_member join player on player_id = player.id where clan_id = ', varClanId, ' and rank != 5 order by ', varOrder);
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200))
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 5;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from loot join player on player.id = player_id where date_recorded > varDate and loot_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 5) group by player_id having count(*) > 1;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from player_stats join player on player.id = player_id where date_recorded > varDate and stat_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 5) group by player_id having count(*) > 1;
end //
delimiter ;

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200), varDate datetime)
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 5;
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'LV', varLevel);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'TR', varTrophies);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'DO', varDonations);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'RE', varReceived);
end //
delimiter ;

drop procedure if exists p_player_leave_clan;
delimiter //
create procedure p_player_leave_clan(varPlayerId int)
begin
	update clan_member set rank = 5, date_modified = NOW(), date_left = NOW() where player_id = varPlayerId and rank != 5;
end //
delimiter ;