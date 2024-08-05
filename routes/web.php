<?php

use App\Http\Controllers\AddDataPariwisataController;
use App\Http\Controllers\AddEvidenceCodeController;
use App\Http\Controllers\AddMasterJournalController;
use App\Http\Controllers\AddUserController;
use App\Http\Controllers\BookBusExternalController;
use App\Http\Controllers\BookedBusController;
use App\Http\Controllers\CashInController;
use App\Http\Controllers\CashInFormController;
use App\Http\Controllers\CashOutController;
use App\Http\Controllers\cashOutFormController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ClosedBalanceController;
use App\Http\Controllers\CorrectingEntryController;
use App\Http\Controllers\CountPayrollController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\editDataPariwisataController;
use App\Http\Controllers\EditMasterJournalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EvidenceCodeController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\GenerateFinancialStatementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\MasterJournalController;
use App\Http\Controllers\PariwisataController;
use App\Http\Controllers\PariwisataExternalController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PelunasanPariwisataController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ViewCashInController;
use App\Http\Controllers\ViewCashOutController;
use App\Http\Controllers\ViewClosedbalanceController;
use App\Http\Controllers\ViewMasterJournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// buat role mana aja yang boleh dan yang tidak
Route::group(['middleware' => 'auth'], function () {

	Route::get('/', [HomeController::class, 'home']);
	Route::prefix('dashboard')->group(function () {
		Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
		Route::get('/listBookDashboard', [DashboardController::class, 'listbookDashboard'])->name('listBookDashboard');
	});
	Route::get('/login', function () {
		return view('dashboard');
	});

	Route::get('/list-penjualan-harian-vs-pariwisata', [DashboardController::class, 'listPenjualanHarianVsPariwisata'])->name('listPenjualanHarianVsPariwisata');

	Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);

	Route::get('/email/verify', function () {
		return view('auth.verify-email');
	})->name('verification.notice');

	Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
		$request->fulfill();
		return redirect('/');
	})->name('verification.verify');

	Route::post('/email/verification-notification', function (Request $request) {
		$request->user()->sendEmailVerificationNotification();
		return back()->with('message', 'Verification link sent!');
	})->middleware(['throttle:6,1'])->name('verification.send');

	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	Route::get('rtl', function () {
		return view('rtl');
	})->name('rtl');


	// PERSONALIA
	Route::resource('employee', EmployeeController::class)->only(
		'index',
		'store',
		'update',
		'destroy'
	);
	route::resource('payroll', PayrollController::class);

	// ACCOUNTING 
	Route::get('export/income-statement', [GenerateFinancialStatementController::class, 'exportIncomeStatement'])->name('export.income-statement');

	Route::get('/exportexcel', [EmployeeController::class, 'exportexcel'])->name('exportexcel');
	Route::post('/import-employee', [EmployeeController::class, 'importEmployee'])->name('import-employee');

	Route::get('/exportMasterAccountToExcel', [ChartOfAccountController::class, 'exportMasterAccountToExcel'])->name('exportMasterAccountToExcel');

	Route::post('/import-account', [ChartOfAccountController::class, 'importAccount'])->name('import-account');

	Route::resource('chart-of-account',  ChartOfAccountController::class)->only(
		'index',
		'store',
		'update',
		'destroy'
	);


	Route::prefix('view-cash-in')->group(function () {
		Route::get('{id}', [ViewCashInController::class, 'index'])->name('view-cash-in.index');
		Route::get('{id}/generate-pdf', [ViewCashInController::class, 'generatePdf'])->name('view-cash-in.generatePdf');
	});

	Route::prefix('view-cash-out')->group(function () {
		Route::get('{id}', [ViewCashOutController::class, 'index'])->name('view-cash-out.index');
	});

	Route::get('cash-out-form', [cashOutFormController::class, 'index'])->name('cash-out-form.index');

	Route::get('cash-in-form', [CashInFormController::class, 'index'])->name('cash-in-form.index');

	Route::resource('cash-in',  CashInController::class)->only(
		'index',
		'store'
	);

	Route::prefix('cash-out')->group(function () {
		Route::get('/', [CashOutController::class, 'index'])->name('cash-out.index');
		Route::post('/', [CashOutController::class, 'store'])->name('cash-out.store');
	});

	Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

	Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

	Route::get('reporting', [ReportingController::class, 'index'])->name('reporting.index');;

	Route::prefix('financial-statement')->group(function () {
		Route::post('/income', [GenerateFinancialStatementController::class, 'income'])->name('generate-financial-statement.income');
		Route::post('/balance', [GenerateFinancialStatementController::class, 'balance'])->name('generate-financial-statement.balance');
		Route::post('/perubahanModal', [GenerateFinancialStatementController::class, 'perubahanModal'])->name('generate-financial-statement.perubahanModal');
	});

	Route::prefix('general-ledger')->group(function () {
		Route::get('/', [GeneralLedgerController::class, 'index'])->name('general-ledger.index');
		Route::get('/filter', [GeneralLedgerController::class, 'getRequest'])->name('general-ledger.getRequest');
	});

	Route::resource('customer', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);

	Route::resource('master-journal', MasterJournalController::class)->only('index', 'store', 'update', 'destroy');

	Route::get('/add-master-journal', [AddMasterJournalController::class, 'index'])->name('add-master-journal.index');

	Route::get('edit-master-journal/{code}', [EditMasterJournalController::class, 'index'])->name('edit-master-journal.index');
	Route::get('view-master-journal/{code}', [ViewMasterJournalController::class, 'index'])->name('view-master-journal.index');

	Route::prefix('correcting-entry')->group(function () {
		Route::get('{id}', [CorrectingEntryController::class, 'index'])->name('correcting-entry.index');
		Route::post('store/{id}', [CorrectingEntryController::class, 'store'])->name('correcting-entry.store');
	});

	Route::prefix('pelunasan-pariwisata')->group(function () {
		Route::get('{id}', [PelunasanPariwisataController::class, 'index'])->name('pelunasan-pariwisata.index');
		Route::post('store/{id}', [PelunasanPariwisataController::class, 'store'])->name('pelunasan-pariwisata.store');
	});

	Route::resource('evidence-code', EvidenceCodeController::class)->only(['index', 'store', 'update', 'destroy']);

	Route::resource('closed-balance', ClosedBalanceController::class);

	Route::get('add-evidence-code', function () {
		return view('user-accounting.add-evidence-code');
	});


	// ADMIN
	Route::resource('pariwisata', PariwisataController::class)->only(['index', 'store', 'update', 'destroy']);
	Route::get('add-data-pariwisata', [AddDataPariwisataController::class, 'index'])->name('add-data-pariwisata.index');
	Route::get('edit-data-pariwisata/{id}', [editDataPariwisataController::class, 'index'])->name('edit-data-pariwisata.index');
	// Route::put('pariwisata/{id}', [editDataPariwisataController::class, 'update'])->name('pariwisata.update');

	Route::prefix('pesan-bus')->group(function () {
		Route::get('/list', [BookedBusController::class, 'listBook'])->name('pesan-bus.list');
		Route::get('/', [BookedBusController::class, 'index'])->name('pesan-bus.index');
		Route::post('/', [BookedBusController::class, 'store'])->name('pesan-bus.store');
		Route::put('/{id}', [BookedBusController::class, 'update'])->name('pesan-bus.update');
		Route::delete('/{id}', [BookedBusController::class, 'destroy'])->name('pesan-bus.destroy');
	});
});



// role SuperAdmin
Route::group(['middleware' => ['auth', 'check.role:1']], function () {
	Route::resource('add-role', RoleController::class)->only(['index', 'store']);
	Route::prefix('add-user')->group(function () {
		Route::get('destroy', [AddUserController::class, 'destroy'])->name('add-user.destroy');
		Route::get('update', [AddUserController::class, 'update'])->name('add-user.update');
		Route::post('store', [AddUserController::class, 'store'])->name('add-user.store');
		Route::get('/', [AddUserController::class, 'index'])->name('add-user.index');
	});

	Route::prefix('user-management')->group(function () {
		Route::get('/', [UserManagementController::class, 'index'])->name('user-management.index');
		Route::post('store', [UserManagementController::class, 'store'])->name('user-management.store');
		Route::delete('/{id}', [UserManagementController::class, 'userDestroy'])->name('user-management.userDestroy');
	});
});

// user external
Route::prefix('book-bus-external')->group(function () {
	Route::get('/', [BookBusExternalController::class, 'index'])->name('book-bus-external.index');
	Route::get('/list', [BookBusExternalController::class, 'listBook'])->name('book-bus-external.list');
});
Route::get('pariwisata-external', [PariwisataExternalController::class, 'index'])->name('pariwisata-external.index');
Route::get('/landing-page', function () {
	return view('customer.landing-page-cust');
});


Route::group(['middleware' => 'guest'], function () {
	Route::get('/register', [RegisterController::class, 'create']);
	Route::post('/register', [RegisterController::class, 'store']);
	Route::get('/login', [SessionsController::class, 'create']);
	Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/login', function () {
	return view('session/login-session');
})->name('login');
