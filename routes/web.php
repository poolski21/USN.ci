<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
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
    Route::get('/profil/connexions/{handle?}', fn() => redirect()->route('profil.show'))->name('profil.connexions');
    Route::post('/profil/{handle}/invitation', [SocialController::class, 'sendFriendRequest'])->name('friend.requests.send');
    Route::post('/friend-requests/{id}/accepter', [SocialController::class, 'acceptFriendRequest'])->name('friend.requests.accept');
    Route::post('/friend-requests/{id}/refuser', [SocialController::class, 'declineFriendRequest'])->name('friend.requests.decline');
    Route::post('/friend-requests/{id}/supprimer', [SocialController::class, 'removeFriend'])->name('friend.requests.remove');
    Route::get('/messages', [SocialController::class, 'messages'])->name('messages');
    Route::get('/messages/{handle}', [SocialController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages/{handle}/read', [SocialController::class, 'markMessagesRead'])->name('messages.read');
    Route::post('/messages/{handle}', [SocialController::class, 'sendMessage'])->name('messages.send');
    Route::get('/notifications', [SocialController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/lu', [SocialController::class, 'markNotificationRead'])->name('notifications.read');
    Route::get('/search', [AuthController::class, 'searchFriends'])->name('search');
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/stage', fn() => view('stage'))->name('stage');
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
    Route::get('/profil/{handle?}', [AuthController::class, 'showProfil'])->name('profil.show');
});

