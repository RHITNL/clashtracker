drop table if exists proxy_request_count;
create table proxy_request_count(
	count int default 0,
	month varchar(10) not null,
	monthly_limit int not null,
	env varchar(200) not null,
	primary key(env)
);

drop procedure if exists p_proxy_request_get;
delimiter //
create procedure p_proxy_request_get()
begin
	select * from proxy_request_count;
end //
delimiter ;

drop procedure if exists p_proxy_request_count_update;
delimiter //
create procedure p_proxy_request_count_update(varEnv varchar(200), varCount int, varMonth varchar(10))
begin
	update proxy_request_count set count = varCount, month = varMonth where env = varEnv;
end //
delimiter ;

insert into proxy_request_count values ('319', 'February', '250', 'http://quotaguard4826:ba0ab104caa7@us-east-1-static-hopper.quotaguard.com:9293');
insert into proxy_request_count values ('0', 'February', '500', 'http://fixie:1E0mdkRzgK49WMj@velodrome.usefixie.com:80');
insert into api_keys values('54.173.229.200', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjkyYzY2NmUzLTA2OWEtNDJlZC1iZjBkLTg3OTQ5ZDQ4ZTE2NSIsImlhdCI6MTQ1NjYxNDk0Mywic3ViIjoiZGV2ZWxvcGVyL2ZmNGVmODA4LWE5MWItMDQ3MS0xYTllLWY1NzAwYmNlOWI3MyIsInNjb3BlcyI6WyJjbGFzaCJdLCJsaW1pdHMiOlt7InRpZXIiOiJkZXZlbG9wZXIvc2lsdmVyIiwidHlwZSI6InRocm90dGxpbmcifSx7ImNpZHJzIjpbIjU0LjE3NS4yMzAuMjUyIiwiNTQuMTczLjIyOS4yMDAiXSwidHlwZSI6ImNsaWVudCJ9XX0.psHlBB6PabvrgRdykOtYoJkDNvjSmQs0YcSkxwBRkXjpOhhUDJ-LzcVXM_yWZNxW7OypGWTK-5mpp1S6iYN_5w');
insert into api_keys values('54.175.230.252', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiIsImtpZCI6IjI4YTMxOGY3LTAwMDAtYTFlYi03ZmExLTJjNzQzM2M2Y2NhNSJ9.eyJpc3MiOiJzdXBlcmNlbGwiLCJhdWQiOiJzdXBlcmNlbGw6Z2FtZWFwaSIsImp0aSI6IjkyYzY2NmUzLTA2OWEtNDJlZC1iZjBkLTg3OTQ5ZDQ4ZTE2NSIsImlhdCI6MTQ1NjYxNDk0Mywic3ViIjoiZGV2ZWxvcGVyL2ZmNGVmODA4LWE5MWItMDQ3MS0xYTllLWY1NzAwYmNlOWI3MyIsInNjb3BlcyI6WyJjbGFzaCJdLCJsaW1pdHMiOlt7InRpZXIiOiJkZXZlbG9wZXIvc2lsdmVyIiwidHlwZSI6InRocm90dGxpbmcifSx7ImNpZHJzIjpbIjU0LjE3NS4yMzAuMjUyIiwiNTQuMTczLjIyOS4yMDAiXSwidHlwZSI6ImNsaWVudCJ9XX0.psHlBB6PabvrgRdykOtYoJkDNvjSmQs0YcSkxwBRkXjpOhhUDJ-LzcVXM_yWZNxW7OypGWTK-5mpp1S6iYN_5w');