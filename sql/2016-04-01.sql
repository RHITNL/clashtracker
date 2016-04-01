drop procedure if exists p_player_record_loot;
delimiter // 
create procedure p_player_record_loot(varId int, varType varchar(2), varAmount int, varDate varchar(50))
begin
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, varType, varAmount);
end //
delimiter ;

drop procedure if exists p_player_get_stats;
delimiter // 
create procedure p_player_get_stats(varId int)
begin
	select * from player_stats where player_id = varId order by date_recorded desc;
end //
delimiter ;

drop procedure if exists p_player_set;
delimiter //
create procedure p_player_set(varId int, varKey varchar(40), varValue text, varDate datetime)
begin
	set @st := concat('update player set ', varKey, ' = ', quote(varValue), ', date_modified = ', quote(varDate), ' where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_player_create;
delimiter //
create procedure p_player_create(varName varchar(50), varTag varchar(15), varDate datetime)
begin
	insert into player(name, tag, date_created) values(varName, varTag, varDate);
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_clan_create;
delimiter //
create procedure p_clan_create(varName varchar(50), varTag varchar(15), varDescription varchar(256), varType varchar(2), varMinimumTrophies int, varWarFrequency varchar(2), varDate datetime)
begin
	insert into clan(name, tag, description, clan_type, minimum_trophies, war_frequency, date_created) values(varName, varTag, varDescription, varType, varMinimumTrophies, varWarFrequency, varDate);
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_clan_set;
delimiter //
create procedure p_clan_set(varId int, varKey varchar(40), varValue text, varDate datetime)
begin
	set @st := concat('update clan set ', varKey, ' = ', quote(varValue), ', date_modified = ', quote(varDate), ' where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_war_create;
delimiter //
create procedure p_war_create(varFirstClanId int, varSecondClanId int, varSize int, varDate datetime)
begin
	insert into war(first_clan_id, second_clan_id, size, date_created) values(varFirstClanId, varSecondClanId, varSize, varDate);
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_war_add_player;
delimiter //
create procedure p_war_add_player(varWarId int, varPlayerId int, varClanId int, varDate datetime)
begin
	insert into war_player(war_id, player_id, clan_id, date_created) values(varWarId, varPlayerId, varClanId, varDate);
	update war set date_modified = varDate where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_remove_player;
delimiter //
create procedure p_war_remove_player(varWarId int, varPlayerId int, varDate datetime)
begin
	delete from war_player where war_id = varWarId and player_id = varPlayerId;
	update war set date_modified = varDate where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_add_attack;
delimiter //
create procedure p_war_add_attack(varWarId int, varAttackerId int, varDefenderId int, varAttackerClanId int, varDefenderClanId int, varStars int, varDate datetime)
begin
if exists (select * from war_attack where war_id = varWarId and attacker_id = varAttackerId and defender_id = varDefenderId)
	then update war_attack set stars = varStars, date_modified = varDate where war_id = varWarId and attacker_id = varAttackerId and defender_id = varDefenderId;
	else insert into war_attack(war_id, attacker_id, defender_id, attacker_clan_id, defender_clan_id, stars, date_created) values (varWarId, varAttackerId, varDefenderId, varAttackerClanId, varDefenderClanId, varStars, varDate);
end if;
update war set date_modified = varDate where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_remove_attack;
delimiter //
create procedure p_war_remove_attack(varWarId int, varAttackerId int, varDefenderId int, varDate datetime)
begin
	delete from war_attack where war_id = varWarId and attacker_id = varAttackerId and defender_id = varDefenderId;
	update war set date_modified = varDate where id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_set;
delimiter //
create procedure p_war_set(varId int, varKey varchar(40), varValue text, varDate datetime)
begin
	set @st := concat('update war set ', varKey, ' = ', quote(varValue), ', date_modified = ', quote(varDate), ' where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_war_update_player_rank;
delimiter //
create procedure p_war_update_player_rank(varWarId int, varPlayerId int, varRank int, varDate datetime)
begin
	update war_player set rank = varRank where war_id = varWarId and player_id = varPlayerId;
	update war set date_modified = varDate where id = varWarId;
end //
delimiter ;

drop procedure if exists p_user_create;
delimiter //
create procedure p_user_create(varEmail varchar(254), varPassword varchar(255), varDate datetime)
begin
	insert into user(email, password, date_created) values(varEmail, varPassword, varDate);
	select last_insert_id() as id;
end //
delimiter ;

drop procedure if exists p_user_change_password;
delimiter //
create procedure p_user_change_password(varId int, varPassword varchar(255), varDate datetime)
begin
	update user set password = varPassword, date_modified = varDate where id = varId;
end //
delimiter ;

drop procedure if exists p_user_link_player;
delimiter //
create procedure p_user_link_player(varUserId int, varPlayerId int, varDate datetime)
begin
	update user set player_id = varPlayerId, date_modified = varDate where id = varUserId;
end //
delimiter ;

drop procedure if exists p_user_unlink_player;
delimiter //
create procedure p_user_unlink_player(varUserId int, varDate datetime)
begin
	update user set player_id = null, date_modified = varDate where id = varUserId;
end //
delimiter ;

drop procedure if exists p_user_set;
delimiter //
create procedure p_user_set(varId int, varKey varchar(40), varValue text, varDate datetime)
begin
	set @st := concat('update user set ', varKey, ' = ', quote(varValue), ', date_modified = ', quote(varDate), ' where id = ', quote(varId));
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_user_unlink_clan;
delimiter //
create procedure p_user_unlink_clan(varUserId int, varDate datetime)
begin
	update user set clan_id = null, date_modified = varDate where id = varUserId;
end //
delimiter ;

drop procedure if exists p_user_link_clan;
delimiter //
create procedure p_user_link_clan(varUserId int, varClanId int, varDate datetime)
begin
	update user set clan_id = varClanId, date_modified = varDate where id = varUserId;
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varApiInfo varchar(50000), varHourAgo datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified, api_info=varApiInfo where id = varId;
	if (varClanPoints <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'CP' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CP', varClanPoints);
	end if;
	if (varClanLevel <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'CL' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CL', varClanLevel);
	end if;
	if (varMembers <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'ME' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'ME', varMembers);
	end if;
	if (varWarWins <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'WW' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'WW', varWarWins);
	end if;
	update clan set api_info = null where api_info is not null and date_modified < varHourAgo;
end //
delimiter ;

drop procedure if exists p_clan_add_player;
delimiter //
create procedure p_clan_add_player(varClanId int, varPlayerId int, varRank varchar(2), varDate datetime)
begin
if exists (select * from clan_member where clan_id = varClanId and player_id = varPlayerId)
	then 
	if (varRank = 5)
	    then update clan_member set rank = varRank, date_modified = varDate, date_left = varDate where clan_id = varClanId and player_id = varPlayerId;
	    else update clan_member set rank = varRank, date_modified = varDate where clan_id = varClanId and player_id = varPlayerId;
	end if;
	else insert into clan_member(clan_id, player_id, rank, date_created) values(varClanId, varPlayerId, varRank, varDate);
end if;
end //
delimiter ;

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200), varDate datetime)
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 5;
	if (varLevel <> (select stat_amount from player_stats where player_id = varId and stat_type = 'LV' order by date_recorded desc limit 1))
		then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'LV', varLevel);
	end if;
	if (varTrophies <> (select stat_amount from player_stats where player_id = varId and stat_type = 'TR' order by date_recorded desc limit 1))
		then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'TR', varTrophies);
	end if;
	if (varDonations <> (select stat_amount from player_stats where player_id = varId and stat_type = 'DO' order by date_recorded desc limit 1))
		then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'DO', varDonations);
	end if;
	if (varReceived <> (select stat_amount from player_stats where player_id = varId and stat_type = 'RE' order by date_recorded desc limit 1))
		then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'RE', varReceived);
	end if;
end //
delimiter ;

drop procedure if exists p_player_leave_clan;
delimiter //
create procedure p_player_leave_clan(varPlayerId int, varDate datetime)
begin
	update clan_member set rank = 5, date_modified = varDate, date_left = varDate where player_id = varPlayerId and rank != 5;
end //
delimiter ;