<?php

$dbHost = 'aws-0-ap-northeast-1.pooler.supabase.com';
$dbUser = 'postgres.xzfvbhmmigctvuucssaa';

$dbPassword = 'GojoSatoru#1';

$conn = pg_connect("host=$dbHost port=5432 dbname=postgres user=$dbUser password=$dbPassword sslmode=require");

if (!$conn) {
    exit('Database connection failed.');
}
