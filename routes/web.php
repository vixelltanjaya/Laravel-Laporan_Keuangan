<?php

use App\Http\Controllers\AddDataPariwisataController;
use App\Http\Controllers\AddEvidenceCodeController;
use App\Http\Controllers\AddMasterJournalController;
use App\Http\Controllers\BookedBusController;
use App\Http\Controllers\CashInController;
use App\Http\Controllers\CashInFormController;
use App\Http\Controllers\CashOutController;
use App\Http\Controllers\cashOutFormController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\ClosedBalanceController;
use App\Http\Controllers\CountPayrollController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EditMasterJournalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EvidenceCodeController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\MasterJournalController;
use App\Http\Controllers\PariwisataController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\ResetController;
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


Route::group(['middleware' => 'auth'], function () {

	Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard', function () {
		return view('dashboard');
	})->name('dashboard');

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

	route::resource('payroll', PayrollController::class);

	route::resource('count-payroll', CountPayrollController::class);

	Route::resource('employee', EmployeeController::class)->only(
		'index',
		'store',
		'update',
		'destroy'
	);
	Route::get('/exportexcel', [EmployeeController::class, 'exportexcel'])->name('exportexcel');
	Route::post('/import-employee', [EmployeeController::class, 'importEmployee'])->name('import-employee');

	Route::get('/exportMasterAccountToExcel', [ChartOfAccountController::class, 'exportMasterAccountToExcel'])->name('exportMasterAccountToExcel');

	Route::post('/import-account', [ChartOfAccountController::class, 'importAccount'])->name('import-account');


	// Accounting 

	Route::resource('chart-of-account',  ChartOfAccountController::class)->only(
		'index',
		'store',
		'update',
		'destroy'
	);


	Route::prefix('view-cash-in')->group(function () {
		Route::get('{id}', [ViewCashInController::class, 'index'])->name('view-cash-in.index');
		Route::post('cancel/{id}', [ViewCashInController::class, 'cancel'])->name('view-cash-in.cancel');
	});

	Route::prefix('view-cash-out')->group(function () {
		Route::get('{id}', [ViewCashOutController::class, 'index'])->name('view-cash-out.index');
		Route::post('cancel/{id}', [ViewCashOutController::class, 'cancel'])->name('view-cash-out.cancel');
	});

	Route::get('cash-out-form', [cashOutFormController::class, 'index'])->name('cash-out-form.index');

	Route::get('cash-in-form', [CashInFormController::class, 'index'])->name('cash-in-form.index');

	Route::resource('cash-in',  CashInController::class)->only(
		'index',
		'store',
		'update',
		'destroy'
	);

	Route::resource('cash-out',  CashOutController::class)->only(
		'index',
		'store',
		'update',
		'destroy'
	);

	Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

	Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

	Route::resource('reporting', ReportingController::class)->only(['index']);

	Route::resource('general-ledger', GeneralLedgerController::class);

	Route::resource('customer', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);

	Route::resource('pariwisata', PariwisataController::class)->only(['index', 'store', 'update', 'destroy']);
	Route::resource('pesan-bus', BookedBusController::class)->only(['index', 'store', 'update', 'destroy']);

	Route::get('add-data-pariwisata', function () {
		return view('add-data-pariwisata');
	});

	Route::resource('master-journal', MasterJournalController::class)->only('index', 'store', 'update', 'destroy');

	Route::get('/add-master-journal', [AddMasterJournalController::class, 'index'])->name('add-master-journal.index');

	Route::get('edit-master-journal/{code}', [EditMasterJournalController::class, 'index'])->name('edit-master-journal.index');
	Route::get('view-master-journal/{code}', [ViewMasterJournalController::class, 'index'])->name('view-master-journal.index');

	Route::resource('user-management', UserManagementController::class);

	Route::resource('evidence-code', EvidenceCodeController::class)->only(['index', 'store', 'update', 'destroy']);

	Route::resource('closed-balance', ClosedBalanceController::class);

	Route::get('add-evidence-code', function () {
		return view('user-accounting.add-evidence-code');
	});

	Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);
	Route::get('/login', function () {
		return view('dashboard');
	});
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
