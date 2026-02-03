SET TIMEZONE = 'Europe/Amsterdam';

-- Owners can administrate and delete their project, 1 owner per project.
-- Admins are the same as owners, except not being able to delete the project their part of.
CREATE TYPE role_enum AS ENUM ('Member', 'Admin', 'Owner');
CREATE TYPE task_status_enum AS ENUM ('Backlog', 'ToDo', 'Doing', 'Review', 'Done');
CREATE TYPE task_priority_enum AS ENUM ('None', 'Low', 'Medium', 'High');


-- START: Users
CREATE TABLE IF NOT EXISTS users
(
    id            SERIAL PRIMARY KEY,
    username      VARCHAR(32)  NOT NULL UNIQUE,
    password_hash TEXT         NOT NULL, -- Ensures no padding compared to VARCHAR
    email         VARCHAR(256) NOT NULL UNIQUE,
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);
-- END: Users


-- START: Projects
CREATE TABLE IF NOT EXISTS projects
(
    id          SERIAL PRIMARY KEY,
    owner_id    INT         NOT NULL REFERENCES users (id), -- Set since only one owner per project, easier access
    name        VARCHAR(32) NOT NULL UNIQUE,
    description VARCHAR(256),
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_projects_owner_id
    ON projects (owner_id);
-- END: Projects


-- START: Project Members
CREATE TABLE IF NOT EXISTS project_members
(
    project_id INT         NOT NULL,
    user_id    INT         NOT NULL,
    role       role_enum   NOT NULL DEFAULT 'Member',
    joined_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    PRIMARY KEY (project_id, user_id),

    CONSTRAINT fk_project FOREIGN KEY (project_id)
        REFERENCES projects (id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user FOREIGN KEY (user_id)
        REFERENCES users (id)
        ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_project_members_user
    ON project_members (user_id);

CREATE INDEX IF NOT EXISTS idx_project_members_user_role
    ON project_members (user_id, role);

CREATE UNIQUE INDEX IF NOT EXISTS idx_one_owner_per_project
    ON project_members (project_id)
    WHERE role = 'Owner';
-- END: Project Members


-- START: Project Invites
CREATE TABLE IF NOT EXISTS project_invites
(
    id          SERIAL PRIMARY KEY,
    project_id  INT         NOT NULL,

    invite_code CHAR(16)    NOT NULL,
    expires_at  TIMESTAMPTZ NOT NULL,
    used_at     TIMESTAMPTZ NULL,

    created_by  INT         NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_project FOREIGN KEY (project_id)
        REFERENCES projects (id)
        ON DELETE CASCADE,

    CONSTRAINT fk_creator FOREIGN KEY (created_by)
        REFERENCES users (id)
        ON DELETE CASCADE
);
CREATE UNIQUE INDEX IF NOT EXISTS idx_project_invites_token
    ON project_invites (invite_code);

CREATE INDEX IF NOT EXISTS idx_project_invites_expires
    ON project_invites (expires_at)
    WHERE used_at IS NULL;
-- END: Project Invites

-- START: Project Tasks
CREATE TABLE IF NOT EXISTS tasks
(
    task_id     SERIAL PRIMARY KEY,
    project_id  INT                NOT NULL,

    title       VARCHAR(256)       NOT NULL,
    description TEXT,
    status      task_status_enum   NOT NULL DEFAULT 'Backlog',
    priority    task_priority_enum NOT NULL DEFAULT 'None',

    created_by  INT                NOT NULL,
    created_at  TIMESTAMPTZ        NOT NULL DEFAULT NOW(),
    assignee_id INT,
    due_date    TIMESTAMPTZ        NOT NULL,

    CONSTRAINT fk_project FOREIGN KEY (project_id)
        REFERENCES projects (id)
        ON DELETE CASCADE,

    CONSTRAINT fk_creator FOREIGN KEY (created_by)
        REFERENCES users (id)
        ON DELETE CASCADE,

    CONSTRAINT fk_assignee FOREIGN KEY (assignee_id)
        REFERENCES users (id)
        ON DELETE SET NULL
);
CREATE INDEX IF NOT EXISTS idx_tasks_project_id
    ON tasks (project_id);

CREATE INDEX IF NOT EXISTS idx_tasks_assignee_id
    ON tasks (assignee_id);

CREATE INDEX IF NOT EXISTS idx_tasks_status
    ON tasks (project_id, status);
-- END: Project Tasks