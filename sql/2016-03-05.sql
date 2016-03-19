drop table if exists loot_report_player_result;
drop table if exists loot_report;

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