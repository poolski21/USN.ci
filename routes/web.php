<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Evenement\EvenementController;
use App\Http\Controllers\SocialController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/connexion', [AuthController::class, 'showConnexion'])->name('connexion');
Route::get('/login', [AuthController::class, 'showConnexion'])->name('login');
Route::post('/connexion', [AuthController::class, 'connexion'])->name('connexion.store');


Route::get('/inscription',[AuthController::class, 'showInscription'])->name('inscription');
Route::post('/inscription',[AuthController::class, 'inscription'])->name('inscription.store');

Route::middleware('auth')->group(function () {
    Route::get('/profil/editer/{handle?}', [AuthController::class, 'showEditProfil'])->name('profil.edit');
    Route::patch('/profil/editer/{handle?}', [AuthController::class, 'updateProfil'])->name('profil.update');
    Route::patch('/profil/couverture/{handle?}', [AuthController::class, 'updateCover'])->name('profil.cover.update');
    Route::patch('/profil/avatar/{handle?}', [AuthController::class, 'updateAvatar'])->name('profil.avatar.update');
    Route::get('/profil/connexions/{handle?}', fn($handle = null) => redirect()->route('profil.show', $handle))->name('profil.connexions');
    Route::post('/profil/{handle}/invitation', [SocialController::class, 'sendFriendRequest'])->name('friend.requests.send');
    Route::get('/demandes-amis', [SocialController::class, 'showFriendRequests'])->name('friend.requests.show');
    Route::post('/friend-requests/{id}/accepter', [SocialController::class, 'acceptFriendRequest'])->name('friend.requests.accept');
    Route::post('/friend-requests/{id}/refuser', [SocialController::class, 'declineFriendRequest'])->name('friend.requests.decline');
    Route::post('/friend-requests/{id}/supprimer', [SocialController::class, 'removeFriend'])->name('friend.requests.remove');
    Route::get('/messages', [SocialController::class, 'messages'])->name('messages');
    Route::get('/messages/{handle}', [SocialController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages/{handle}/read', [SocialController::class, 'markMessagesRead'])->name('messages.read');
    Route::post('/messages/{handle}', [SocialController::class, 'sendMessage'])->name('messages.send');
    Route::patch('/messages/{handle}/message/{message}', [SocialController::class, 'updateMessage'])->name('messages.message.update');
    Route::delete('/messages/{handle}/message/{message}', [SocialController::class, 'deleteMessage'])->name('messages.message.delete');
    Route::post('/messages/{handle}/call', [SocialController::class, 'startCall'])->name('messages.call.start');
    Route::get('/messages/call/incoming', [SocialController::class, 'incomingCall'])->name('messages.call.incoming');
    Route::get('/messages/call/{session}', [SocialController::class, 'showCall'])->name('messages.call');
    Route::post('/messages/call/{session}/offer', [SocialController::class, 'storeCallOffer'])->name('messages.call.offer');
    Route::post('/messages/call/{session}/answer', [SocialController::class, 'storeCallAnswer'])->name('messages.call.answer');
    Route::post('/messages/call/{session}/candidate', [SocialController::class, 'storeCallCandidate'])->name('messages.call.candidate');
    Route::post('/messages/call/{session}/hangup', [SocialController::class, 'hangupCall'])->name('messages.call.hangup');
    Route::post('/messages/call/{session}/reject', [SocialController::class, 'rejectCall'])->name('messages.call.reject');
    Route::get('/messages/call/{session}/status', [SocialController::class, 'callStatus'])->name('messages.call.status');
    Route::get('/notifications', [SocialController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/lu', [SocialController::class, 'markNotificationRead'])->name('notifications.read');
    Route::get('/live-updates', [SocialController::class, 'liveUpdates'])->name('live.updates');
    Route::get('/search', [AuthController::class, 'searchFriends'])->name('search');
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/documents', fn() => view('documents'))->name('documents');
    Route::get('/groupes/nouveau', [SocialController::class, 'showCreateGroup'])->name('groupes.create');
    Route::post('/groupes', [SocialController::class, 'storeGroup'])->name('groupes.store');
    Route::get('/groupes/{slug}', [SocialController::class, 'showGroup'])->name('groupes.show');
    Route::post('/groupes/{slug}/rejoindre', [SocialController::class, 'joinGroup'])->name('groupes.join');
    Route::post('/groupes/{slug}/quitter', [SocialController::class, 'leaveGroup'])->name('groupes.leave');
    Route::post('/groupes/{slug}/membres', [SocialController::class, 'addGroupMember'])->name('groupes.members.add');
    Route::delete('/groupes/{slug}/membres/{userId}', [SocialController::class, 'removeGroupMember'])->name('groupes.members.remove');
    Route::post('/groupes/{slug}/messages', [SocialController::class, 'sendGroupMessage'])->name('groupes.messages.send');
    Route::delete('/groupes/{slug}/messages/{messageId}', [SocialController::class, 'deleteGroupMessage'])->name('groupes.messages.delete');
    Route::post('/posts', [SocialController::class, 'storePost'])->name('posts.store');
    Route::post('/posts/{id}/edit', [SocialController::class, 'editPost'])->name('posts.edit');
    Route::delete('/posts/{id}', [SocialController::class, 'destroyPost'])->name('posts.destroy');
    Route::post('/posts/{id}/like', [SocialController::class, 'likePost'])->name('posts.like');
    Route::post('/posts/{id}/comment', [SocialController::class, 'commentPost'])->name('posts.comment');
    Route::post('/posts/{id}/share', [SocialController::class, 'sharePost'])->name('posts.share');
    Route::get('/evenements', [EvenementController::class, 'index'])->name('evenements.index');
    Route::get('/evenements/creer', [EvenementController::class, 'create'])->name('evenements.create');
    Route::post('/evenements', [EvenementController::class, 'store'])->name('evenements.store');
    Route::get('/evenements/{evenement}', [EvenementController::class, 'show'])->name('evenements.show');
    Route::post('/evenements/{evenement}/like', [EvenementController::class, 'like'])->name('evenements.like');
    Route::post('/evenements/{evenement}/comment', [EvenementController::class, 'comment'])->name('evenements.comment');
    Route::post('/evenements/{evenement}/share', [EvenementController::class, 'share'])->name('evenements.share');
    Route::get('/profil/{handle?}', [AuthController::class, 'showProfil'])->name('profil.show');

    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/developpeur/dashboard/stats', [AdminController::class, 'stats'])->name('admin.dashboard.stats');
});

