<?php

use App\Livewire\HostPanel;
use App\Livewire\HostSession;
use App\Livewire\ParticipantSummaryPanel;
use App\Livewire\Welcome;
use App\Livewire\VotePanel;
use App\Livewire\QuestionDisplay;
use App\Livewire\SummaryPanel;
use App\Livewire\OverallPanel;
use App\Livewire\CreateQuestion;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Welcome::class)->name('home');

Route::get('/host-session', HostSession::class)->name('host-session');

Route::get('/host-panel/{session}', HostPanel::class)->name('host-panel');

Route::get('/vote-panel/{session}/{participant}/{code}', VotePanel::class)->name('vote-panel');

Route::get('/question-panel/{session}/{participant}/{question}', QuestionDisplay::class)->name('question-panel');

Route::get('/participant-summary-panel/{session}/{participant}/{question}', ParticipantSummaryPanel::class)->name('participant-summary-panel');

Route::get('/summary-panel/{session}/{question}', SummaryPanel::class)->name('summary-panel');

Route::get('/overall-panel/{session}', OverallPanel::class)->name('overall-panel');

Route::get('/create-question/{session}/{participant}/{chosenParticipant}', CreateQuestion::class)->name('create-question');