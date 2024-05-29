<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    
    /**
     * このユーザーが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    

    /**
     * このユーザーに関係するモデルの件数をロードする。
     */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', "favorites"]);
    }
    
    public function followings(){
        return $this->belongsToMany(User::class, "user_follow", "user_id", "follow_id")->withTimestamps();
    }
    
    public function followers(){
        return $this->belongsToMany(User::class, "user_follow", "follow_id", "user_id")->withTimestamps();
    }
    
    public function follow(int $userId){
        
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if($exist || $its_me){
            return false;
        }else{
            $this->followings()->attach($userId);
        }
    }
    
    public function unfollow(int $userId){
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me){
            $this->followings()->detach($userId);
            return true;
        }else{
            return false;
        }
        
    }
    
    public function is_following(int $userId){
        return $this->followings()->where("follow_id", $userId)->exists(); 
    }

    public function feed_microposts()
    {
        // このユーザーがフォロー中のユーザーのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザーのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザーが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    
    public function favorites(){
        return $this->belongsToMany(User::class, "favorites", "user_id", "micropost_id")->withTimestamps();
    }
    
    
    
}
