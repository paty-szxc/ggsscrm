<?php

namespace App\Traits;

trait AuthorizesAccess
{
    /**
     * Check if user has specific permission
     */
    protected function hasPermission($permission): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Admin has all permissions
        if ($user->role === 'Admin') {
            return true;
        }

        $userPermissions = $this->getUserPermissions($user);
        return in_array($permission, $userPermissions);
    }

    /**
     * Check if user has specific role
     */
    protected function hasRole($role): bool
    {
        $user = auth()->user();
        return $user && $user->role === $role;
    }

    /**
     * Get user permissions based on role and emp_code
     */
    private function getUserPermissions($user): array
    {
        $permissions = [];
        $role = $user->role;
        $empCode = $user->emp_code;
        
        //emp code specific permissions
        switch ($empCode) {
            case '0000-0002':
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'quotation_access';
                $permissions[] = 'office_equipment_access';
                $permissions[] = 'survey_equipment_access';
                $permissions[] = 'company_assets_access';
                $permissions[] = 'titles_access';
                $permissions[] = 'gco_access'; //GCO and construction project monitoring access
                break;
                
            case '0000-0004':
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'company_assets_access';
                $permissions[] = 'govt_external_access';
                $permissions[] = 'titles_access';
                $permissions[] = 'gco_access'; //GCO and construction project monitoring access
                break;

            case '0000-0016':
                //handled in the 'Encoder' role logic below, as it's a special case
                break;
        }
        
        //role-based permissions, including the new Encoder rules
        if($role === 'Encoder'){
            if($empCode === '0000-0016'){
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'survey_equipment_access';
                $permissions[] = 'govt_external_access';
                $permissions[] = 'titles_access';
            }
            else{ // Encoder but not 0000-0016
                $permissions[] = 'survey_monitoring_access';
                $permissions[] = 'survey_equipment_access';
                $permissions[] = 'titles_access';
            }
        }
        elseif($role === 'Checker'){
            //checkers have specific access to survey equipment
            if($empCode === '0000-0011'){
                $permissions[] = 'survey_equipment_access';
            }
        }

        return array_unique($permissions);
    }

    /**
     * Authorize access or abort with 403
     */
    protected function authorizePermission($permission): void
    {
        if (!$this->hasPermission($permission)) {
            abort(403, 'Access denied. Insufficient permissions.');
        }
    }

    /**
     * Authorize role or abort with 403
     */
    protected function authorizeRole($role): void
    {
        if (!$this->hasRole($role)) {
            abort(403, 'Access denied. Insufficient permissions.');
        }
    }
}
