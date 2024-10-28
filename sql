-- Tabela de Usuários (com campo de último login e indicação de administrador)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,        -- Indica se o usuário é administrador
    last_login TIMESTAMP NULL,             -- Armazena a última vez que o usuário fez login
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Requisitos (para armazenar requisitos de modelagem de banco de dados)
CREATE TABLE requirements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,                           -- Relacionamento com a tabela de usuários
    description TEXT NOT NULL,             -- Descrição dos requisitos do banco de dados
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabela de Modelos (para armazenar os modelos de banco de dados criados)
CREATE TABLE models (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,                           -- Relacionamento com a tabela de usuários
    requirement_id INT,                    -- Relacionamento com a tabela de requisitos
    model_name VARCHAR(255) NOT NULL,      -- Nome do modelo criado pelo usuário
    model_data JSON NOT NULL,              -- Estrutura do modelo em formato JSON
    llama_suggestion TEXT,                 -- Sugestão do modelo LLaMA para o banco de dados
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (requirement_id) REFERENCES requirements(id) ON DELETE SET NULL
);

-- Tabela de Logs (para registrar operações realizadas pelos usuários)
CREATE TABLE logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,                           -- Usuário que realizou a operação
    model_id INT,                          -- Relacionamento com o modelo gerado
    action VARCHAR(255) NOT NULL,          -- Descrição da ação (ex.: "Geração de SQL")
    sql_generated TEXT,                    -- SQL gerado pelo usuário
    ip_address VARCHAR(45),                -- Endereço IP do usuário (45 caracteres para IPv6)
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE
);

-- Tabela de Logs de Visitantes Anônimos (para registrar visitas de usuários não logados)
CREATE TABLE visitor_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,       -- IP do visitante
    visit_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Data e hora da visita
);

-- Tabela de Sessões JWT (para persistir sessões e permitir logout)
CREATE TABLE jwt_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    jwt_token TEXT NOT NULL,
    expires_at TIMESTAMP NOT NULL,         -- Controle de validade do token JWT
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Índices e Constraints para melhorar o desempenho das consultas
CREATE INDEX idx_user_email ON users (email);
CREATE INDEX idx_model_user_id ON models (user_id);
CREATE INDEX idx_requirements_user_id ON requirements (user_id);
CREATE INDEX idx_log_user_id ON logs (user_id);
CREATE INDEX idx_visitor_ip ON visitor_logs (ip_address);
CREATE INDEX idx_jwt_user_id ON jwt_sessions (user_id);
