<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserInvitation;

class UserInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:invite {email : The email of the user to invite}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an invitation email to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Check if user exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        // Get user's archives
        $archives = $user->archives->toArray();
        
        // Send the invitation
        try {
            // Pass user data to the notification
            $data = [
                'email' => $user->email,
                'name' => $user->name,
                'firstname' => $user->firstname,
                'archives' => $archives
            ];
            
            Notification::route('mail', $user->email)->notify(new UserInvitation($data));
            $this->info("Invitation sent to {$email} successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to send invitation: " . $e->getMessage());
            return 1;
        }
    }
}
