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
	name varchar(50) not null,
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
	then select player_id from clan_member where clan_id = varClanId order by date_left desc, date_created;
	else select player_id from clan_member where clan_id = varClanId and rank = varRank order by date_left desc, date_created;
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
	select id from clan limit varPageSize;
end //
delimiter ;

drop procedure if exists p_get_players;
delimiter //
create procedure p_get_players(varPageSize int)
begin
	select id from player limit varPageSize;
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