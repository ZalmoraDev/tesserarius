SET TIMEZONE = 'Europe/Amsterdam';

-- Owners can administrate and delete their project, 1 owner per project.
-- Admins are the same as owners, except not being able to delete the project their part of.
CREATE TYPE role_enum AS ENUM ('member', 'admin', 'owner');

-- START: Users
CREATE TABLE IF NOT EXISTS users
(
    id            SERIAL PRIMARY KEY,
    username      VARCHAR(32)  NOT NULL UNIQUE,
    password_hash TEXT         NOT NULL, -- Ensures no padding
    email         VARCHAR(256) NOT NULL UNIQUE,
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);
-- Default password = admin
INSERT INTO users (username, password_hash, email)
VALUES ('admin', 'TO BE HASHED', 'john@doe.com')
ON CONFLICT DO NOTHING;
-- END: Users

-- START: Projects
CREATE TABLE IF NOT EXISTS projects
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(32)  NOT NULL UNIQUE,
    description VARCHAR(256) NOT NULL,
    created_at  TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);
INSERT INTO projects (name, description)
VALUES ('Tesserarius', 'Task manager')
ON CONFLICT DO NOTHING;
-- END: Projects

-- START: Project Members
CREATE TABLE IF NOT EXISTS project_members
(
    project_id INT         NOT NULL,
    user_id    INT         NOT NULL,
    role       role_enum   NOT NULL DEFAULT 'member',
    joined_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    PRIMARY KEY (project_id, user_id),

    CONSTRAINT fk_project FOREIGN KEY (project_id)
        REFERENCES projects (id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user FOREIGN KEY (user_id)
        REFERENCES users (id)
        ON DELETE CASCADE
);
CREATE INDEX idx_project_members_user
    ON project_members (user_id);

CREATE INDEX idx_project_members_user_role
    ON project_members (user_id, role);

CREATE UNIQUE INDEX idx_one_owner_per_project
    ON project_members (project_id)
    WHERE role = 'owner';
-- END: Project Members

-- START: Project Invites
CREATE TABLE IF NOT EXISTS project_invites
(
    id         SERIAL PRIMARY KEY,
    project_id INT         NOT NULL,

    token_hash CHAR(64)    NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,
    used_at    TIMESTAMPTZ NULL,

    created_by INT         NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT fk_project FOREIGN KEY (project_id)
        REFERENCES projects (id)
        ON DELETE CASCADE,

    CONSTRAINT fk_creator FOREIGN KEY (created_by)
        REFERENCES users (id)
        ON DELETE CASCADE
);
CREATE UNIQUE INDEX idx_project_invites_token
    ON project_invites (token_hash);

CREATE INDEX idx_project_invites_expires
    ON project_invites (expires_at)
    WHERE used_at IS NULL;
-- END: Project Invites