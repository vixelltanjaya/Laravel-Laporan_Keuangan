@echo off
SETLOCAL

REM Space-separated list of migration files
SET migrations=^
2024_03_20_151058_employee_table ^
2024_03_24_154001_role_table ^
2024_03_28_042836_chart_of_account_table ^
2024_03_31_125539_permission ^
2024_03_31_130058_permissions_role ^
2024_03_31_130115_permissions_user ^
2024_03_31_130123_role_user ^
2024_05_12_035622_customer ^
2024_05_23_055548_journal_entry ^
2024_06_10_204335_detail_journal_entry ^
2024_06_13_225836_account_balance ^
2024_06_13_225849_master_transaction ^
2024_06_13_225901_detail_master_transaction ^
2024_06_13_233714_division ^
2024_06_13_234542_evidence_code ^
2024_06_14_000124_bis_pariwisata ^
2024_06_14_082602_closing_balance ^
2024_07_12_230454_surat_jalan ^
2024_07_16_162903_booking_bus

REM Path to the migrations folder
SET migrations_path=database\migrations\

REM Loop through each migration file
FOR %%i IN (%migrations%) DO (
    echo Running migration: %%i
    php artisan migrate --path=%migrations_path%%%i.php
)

ENDLOCAL
pause
