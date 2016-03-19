alter table player add first_attack_total_stars int default 0;
alter table player add first_attack_new_stars int default 0;
alter table player add second_attack_total_stars int default 0;
alter table player add second_attack_new_stars int default 0;
alter table player add stars_on_defence int default 0;
alter table player add number_of_defences int default 0;
alter table player add attacks_used int default 0;
alter table player add number_of_wars int default 0;

drop procedure if exists p_war_get_attacks;
delimiter //
create procedure p_war_get_attacks(varWarId int)
begin
	select * from war_attack where war_id = varWarId order by date_created;
end //
delimiter ;