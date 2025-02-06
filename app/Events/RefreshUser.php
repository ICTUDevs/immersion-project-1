
        $admins = User::role(['superadmin', 'administrator', 'timekeeper'])->get();

        if ($admins->isNotEmpty()) {
            broadcast(new RefreshUser($admins->all()));
        } else {
            Log::error('No admin user found');
        }