<?php

namespace App\Repositories\Exceptions;

/** Exception for project members repository operations. */
final class ProjectMembersRepoException extends RepositoryException
{
    // Invite code errors
    public const string INVITE_NOT_FOUND = "Invite code not found";
    public const string INVITE_EXPIRED = "Invite code has expired";
    public const string INVITE_ALREADY_USED = "Invite code has already been used";

    // Member errors
    public const string MEMBER_NOT_FOUND = "Project member not found";
    public const string MEMBER_ALREADY_EXISTS = "User is already a member of this project";

    // Database operation errors
    public const string FAILED_TO_ADD_MEMBER = "Failed to add project member";
    public const string FAILED_TO_REMOVE_MEMBER = "Failed to remove project member";
    public const string FAILED_TO_UPDATE_ROLE = "Failed to update member role";
    public const string FAILED_TO_CREATE_INVITES = "Failed to create invite codes";
    public const string FAILED_TO_DELETE_INVITE = "Failed to delete invite code";
    public const string FAILED_TO_FETCH_MEMBERS = "Failed to fetch project members";
    public const string FAILED_TO_FETCH_INVITES = "Failed to fetch project invites";
}
