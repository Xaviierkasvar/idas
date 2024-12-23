PGDMP  
                    |            asist_student_academic    16.3    16.3 a    o           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                      false            p           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                      false            q           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                      false            r           1262    25079    asist_student_academic    DATABASE     �   CREATE DATABASE asist_student_academic WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Spanish_Colombia.1252';
 &   DROP DATABASE asist_student_academic;
                postgres    false            �            1255    33444    set_current_date()    FUNCTION     �   CREATE FUNCTION public.set_current_date() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.date := CURRENT_DATE;
    RETURN NEW;
END;
$$;
 )   DROP FUNCTION public.set_current_date();
       public          postgres    false            �            1255    25248    set_timestamp()    FUNCTION       CREATE FUNCTION public.set_timestamp() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF (TG_OP = 'INSERT') THEN
        NEW.created_at = CURRENT_TIMESTAMP;
    ELSIF (TG_OP = 'UPDATE') THEN
        NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
    RETURN NEW;
END;
$$;
 &   DROP FUNCTION public.set_timestamp();
       public          postgres    false            �            1259    25133    academic_periods    TABLE     �  CREATE TABLE public.academic_periods (
    id integer NOT NULL,
    name character varying NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer,
    is_active boolean DEFAULT true
);
 $   DROP TABLE public.academic_periods;
       public         heap    postgres    false            �            1259    25132    academic_periods_id_seq    SEQUENCE     �   CREATE SEQUENCE public.academic_periods_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 .   DROP SEQUENCE public.academic_periods_id_seq;
       public          postgres    false    220            s           0    0    academic_periods_id_seq    SEQUENCE OWNED BY     S   ALTER SEQUENCE public.academic_periods_id_seq OWNED BY public.academic_periods.id;
          public          postgres    false    219            �            1259    25290    alerts    TABLE     @  CREATE TABLE public.alerts (
    id integer NOT NULL,
    message text NOT NULL,
    percentage_of_hours integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp without time zone,
    created_by integer,
    updated_by integer,
    name character varying(255)
);
    DROP TABLE public.alerts;
       public         heap    postgres    false            �            1259    25289    alerts_id_seq    SEQUENCE     �   CREATE SEQUENCE public.alerts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 $   DROP SEQUENCE public.alerts_id_seq;
       public          postgres    false    230            t           0    0    alerts_id_seq    SEQUENCE OWNED BY     ?   ALTER SEQUENCE public.alerts_id_seq OWNED BY public.alerts.id;
          public          postgres    false    229            �            1259    25179 
   attendance    TABLE     ]  CREATE TABLE public.attendance (
    id integer NOT NULL,
    course_id integer,
    student_id integer,
    date date NOT NULL,
    present integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer
);
    DROP TABLE public.attendance;
       public         heap    postgres    false            �            1259    25178    attendance_id_seq    SEQUENCE     �   CREATE SEQUENCE public.attendance_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 (   DROP SEQUENCE public.attendance_id_seq;
       public          postgres    false    224            u           0    0    attendance_id_seq    SEQUENCE OWNED BY     G   ALTER SEQUENCE public.attendance_id_seq OWNED BY public.attendance.id;
          public          postgres    false    223            �            1259    25197    course_students    TABLE     %  CREATE TABLE public.course_students (
    course_id integer NOT NULL,
    student_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer
);
 #   DROP TABLE public.course_students;
       public         heap    postgres    false            �            1259    25214    course_teachers    TABLE     %  CREATE TABLE public.course_teachers (
    course_id integer NOT NULL,
    teacher_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer
);
 #   DROP TABLE public.course_teachers;
       public         heap    postgres    false            �            1259    25145    courses    TABLE     �  CREATE TABLE public.courses (
    id integer NOT NULL,
    name character varying NOT NULL,
    academic_period_id integer,
    hours integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer,
    is_active boolean DEFAULT true,
    hours_for_classes integer,
    first_attendance_time timestamp without time zone
);
    DROP TABLE public.courses;
       public         heap    postgres    false            �            1259    25144    courses_id_seq    SEQUENCE     �   CREATE SEQUENCE public.courses_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 %   DROP SEQUENCE public.courses_id_seq;
       public          postgres    false    222            v           0    0    courses_id_seq    SEQUENCE OWNED BY     A   ALTER SEQUENCE public.courses_id_seq OWNED BY public.courses.id;
          public          postgres    false    221            �            1259    25309 
   migrations    TABLE     �   CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);
    DROP TABLE public.migrations;
       public         heap    postgres    false            �            1259    25308    migrations_id_seq    SEQUENCE     �   CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 (   DROP SEQUENCE public.migrations_id_seq;
       public          postgres    false    232            w           0    0    migrations_id_seq    SEQUENCE OWNED BY     G   ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;
          public          postgres    false    231            �            1259    25232    notifications    TABLE     �  CREATE TABLE public.notifications (
    id integer NOT NULL,
    user_id integer,
    message text NOT NULL,
    date_sent timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer,
    role_id integer,
    course_id integer,
    absence_percentage integer
);
 !   DROP TABLE public.notifications;
       public         heap    postgres    false            �            1259    25231    notifications_id_seq    SEQUENCE     �   CREATE SEQUENCE public.notifications_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 +   DROP SEQUENCE public.notifications_id_seq;
       public          postgres    false    228            x           0    0    notifications_id_seq    SEQUENCE OWNED BY     M   ALTER SEQUENCE public.notifications_id_seq OWNED BY public.notifications.id;
          public          postgres    false    227            �            1259    25105    roles    TABLE       CREATE TABLE public.roles (
    id integer NOT NULL,
    name character varying NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer
);
    DROP TABLE public.roles;
       public         heap    postgres    false            �            1259    25104    roles_id_seq    SEQUENCE     �   CREATE SEQUENCE public.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 #   DROP SEQUENCE public.roles_id_seq;
       public          postgres    false    216            y           0    0    roles_id_seq    SEQUENCE OWNED BY     =   ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;
          public          postgres    false    215            �            1259    25116    users    TABLE     �  CREATE TABLE public.users (
    id integer NOT NULL,
    email character varying NOT NULL,
    password character varying NOT NULL,
    role_id integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by integer,
    updated_by integer,
    is_active boolean DEFAULT true,
    name text,
    last_name text,
    document_type character varying(50),
    document_number character varying(50)
);
    DROP TABLE public.users;
       public         heap    postgres    false            �            1259    25115    users_id_seq    SEQUENCE     �   CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 #   DROP SEQUENCE public.users_id_seq;
       public          postgres    false    218            z           0    0    users_id_seq    SEQUENCE OWNED BY     =   ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;
          public          postgres    false    217            �           2604    25136    academic_periods id    DEFAULT     z   ALTER TABLE ONLY public.academic_periods ALTER COLUMN id SET DEFAULT nextval('public.academic_periods_id_seq'::regclass);
 B   ALTER TABLE public.academic_periods ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    219    220    220            �           2604    25293 	   alerts id    DEFAULT     f   ALTER TABLE ONLY public.alerts ALTER COLUMN id SET DEFAULT nextval('public.alerts_id_seq'::regclass);
 8   ALTER TABLE public.alerts ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    230    229    230            �           2604    25182    attendance id    DEFAULT     n   ALTER TABLE ONLY public.attendance ALTER COLUMN id SET DEFAULT nextval('public.attendance_id_seq'::regclass);
 <   ALTER TABLE public.attendance ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    224    223    224            �           2604    25318 
   courses id    DEFAULT     h   ALTER TABLE ONLY public.courses ALTER COLUMN id SET DEFAULT nextval('public.courses_id_seq'::regclass);
 9   ALTER TABLE public.courses ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    221    222    222            �           2604    25312    migrations id    DEFAULT     n   ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);
 <   ALTER TABLE public.migrations ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    231    232    232            �           2604    25235    notifications id    DEFAULT     t   ALTER TABLE ONLY public.notifications ALTER COLUMN id SET DEFAULT nextval('public.notifications_id_seq'::regclass);
 ?   ALTER TABLE public.notifications ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    228    227    228            }           2604    25108    roles id    DEFAULT     d   ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);
 7   ALTER TABLE public.roles ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    216    215    216            �           2604    25119    users id    DEFAULT     d   ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);
 7   ALTER TABLE public.users ALTER COLUMN id DROP DEFAULT;
       public          postgres    false    217    218    218            `          0    25133    academic_periods 
   TABLE DATA           �   COPY public.academic_periods (id, name, start_date, end_date, created_at, updated_at, created_by, updated_by, is_active) FROM stdin;
    public          postgres    false    220   �~       j          0    25290    alerts 
   TABLE DATA           x   COPY public.alerts (id, message, percentage_of_hours, created_at, updated_at, created_by, updated_by, name) FROM stdin;
    public          postgres    false    230   [       d          0    25179 
   attendance 
   TABLE DATA           ~   COPY public.attendance (id, course_id, student_id, date, present, created_at, updated_at, created_by, updated_by) FROM stdin;
    public          postgres    false    224   �       e          0    25197    course_students 
   TABLE DATA           p   COPY public.course_students (course_id, student_id, created_at, updated_at, created_by, updated_by) FROM stdin;
    public          postgres    false    225   ��       f          0    25214    course_teachers 
   TABLE DATA           p   COPY public.course_teachers (course_id, teacher_id, created_at, updated_at, created_by, updated_by) FROM stdin;
    public          postgres    false    226   ��       b          0    25145    courses 
   TABLE DATA           �   COPY public.courses (id, name, academic_period_id, hours, created_at, updated_at, created_by, updated_by, is_active, hours_for_classes, first_attendance_time) FROM stdin;
    public          postgres    false    222   �       l          0    25309 
   migrations 
   TABLE DATA           :   COPY public.migrations (id, migration, batch) FROM stdin;
    public          postgres    false    232   ��       h          0    25232    notifications 
   TABLE DATA           �   COPY public.notifications (id, user_id, message, date_sent, created_at, updated_at, created_by, updated_by, role_id, course_id, absence_percentage) FROM stdin;
    public          postgres    false    228   ��       \          0    25105    roles 
   TABLE DATA           Y   COPY public.roles (id, name, created_at, updated_at, created_by, updated_by) FROM stdin;
    public          postgres    false    216   M�       ^          0    25116    users 
   TABLE DATA           �   COPY public.users (id, email, password, role_id, created_at, updated_at, created_by, updated_by, is_active, name, last_name, document_type, document_number) FROM stdin;
    public          postgres    false    218   ��       {           0    0    academic_periods_id_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.academic_periods_id_seq', 7, true);
          public          postgres    false    219            |           0    0    alerts_id_seq    SEQUENCE SET     ;   SELECT pg_catalog.setval('public.alerts_id_seq', 8, true);
          public          postgres    false    229            }           0    0    attendance_id_seq    SEQUENCE SET     A   SELECT pg_catalog.setval('public.attendance_id_seq', 317, true);
          public          postgres    false    223            ~           0    0    courses_id_seq    SEQUENCE SET     <   SELECT pg_catalog.setval('public.courses_id_seq', 3, true);
          public          postgres    false    221                       0    0    migrations_id_seq    SEQUENCE SET     @   SELECT pg_catalog.setval('public.migrations_id_seq', 1, false);
          public          postgres    false    231            �           0    0    notifications_id_seq    SEQUENCE SET     C   SELECT pg_catalog.setval('public.notifications_id_seq', 51, true);
          public          postgres    false    227            �           0    0    roles_id_seq    SEQUENCE SET     :   SELECT pg_catalog.setval('public.roles_id_seq', 3, true);
          public          postgres    false    215            �           0    0    users_id_seq    SEQUENCE SET     ;   SELECT pg_catalog.setval('public.users_id_seq', 10, true);
          public          postgres    false    217            �           2606    25143 &   academic_periods academic_periods_pkey 
   CONSTRAINT     d   ALTER TABLE ONLY public.academic_periods
    ADD CONSTRAINT academic_periods_pkey PRIMARY KEY (id);
 P   ALTER TABLE ONLY public.academic_periods DROP CONSTRAINT academic_periods_pkey;
       public            postgres    false    220            �           2606    25298    alerts alerts_pkey 
   CONSTRAINT     P   ALTER TABLE ONLY public.alerts
    ADD CONSTRAINT alerts_pkey PRIMARY KEY (id);
 <   ALTER TABLE ONLY public.alerts DROP CONSTRAINT alerts_pkey;
       public            postgres    false    230            �           2606    25186    attendance attendance_pkey 
   CONSTRAINT     X   ALTER TABLE ONLY public.attendance
    ADD CONSTRAINT attendance_pkey PRIMARY KEY (id);
 D   ALTER TABLE ONLY public.attendance DROP CONSTRAINT attendance_pkey;
       public            postgres    false    224            �           2606    25203 $   course_students course_students_pkey 
   CONSTRAINT     u   ALTER TABLE ONLY public.course_students
    ADD CONSTRAINT course_students_pkey PRIMARY KEY (course_id, student_id);
 N   ALTER TABLE ONLY public.course_students DROP CONSTRAINT course_students_pkey;
       public            postgres    false    225    225            �           2606    25220 $   course_teachers course_teachers_pkey 
   CONSTRAINT     u   ALTER TABLE ONLY public.course_teachers
    ADD CONSTRAINT course_teachers_pkey PRIMARY KEY (course_id, teacher_id);
 N   ALTER TABLE ONLY public.course_teachers DROP CONSTRAINT course_teachers_pkey;
       public            postgres    false    226    226            �           2606    25155    courses courses_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY public.courses
    ADD CONSTRAINT courses_pkey PRIMARY KEY (id);
 >   ALTER TABLE ONLY public.courses DROP CONSTRAINT courses_pkey;
       public            postgres    false    222            �           2606    25314    migrations migrations_pkey 
   CONSTRAINT     X   ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);
 D   ALTER TABLE ONLY public.migrations DROP CONSTRAINT migrations_pkey;
       public            postgres    false    232            �           2606    25242     notifications notifications_pkey 
   CONSTRAINT     ^   ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);
 J   ALTER TABLE ONLY public.notifications DROP CONSTRAINT notifications_pkey;
       public            postgres    false    228            �           2606    25114    roles roles_pkey 
   CONSTRAINT     N   ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);
 :   ALTER TABLE ONLY public.roles DROP CONSTRAINT roles_pkey;
       public            postgres    false    216            �           2606    25317    users users_document_number_key 
   CONSTRAINT     e   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_document_number_key UNIQUE (document_number);
 I   ALTER TABLE ONLY public.users DROP CONSTRAINT users_document_number_key;
       public            postgres    false    218            �           2606    25126    users users_pkey 
   CONSTRAINT     N   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);
 :   ALTER TABLE ONLY public.users DROP CONSTRAINT users_pkey;
       public            postgres    false    218            �           2620    33445 #   attendance before_insert_attendance    TRIGGER     �   CREATE TRIGGER before_insert_attendance BEFORE INSERT ON public.attendance FOR EACH ROW EXECUTE FUNCTION public.set_current_date();
 <   DROP TRIGGER before_insert_attendance ON public.attendance;
       public          postgres    false    234    224            �           2620    25249 0   academic_periods set_created_at_academic_periods    TRIGGER     �   CREATE TRIGGER set_created_at_academic_periods BEFORE INSERT ON public.academic_periods FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 I   DROP TRIGGER set_created_at_academic_periods ON public.academic_periods;
       public          postgres    false    220    233            �           2620    25300    alerts set_created_at_alerts    TRIGGER     z   CREATE TRIGGER set_created_at_alerts BEFORE INSERT ON public.alerts FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 5   DROP TRIGGER set_created_at_alerts ON public.alerts;
       public          postgres    false    230    233            �           2620    25253 $   attendance set_created_at_attendance    TRIGGER     �   CREATE TRIGGER set_created_at_attendance BEFORE INSERT ON public.attendance FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 =   DROP TRIGGER set_created_at_attendance ON public.attendance;
       public          postgres    false    233    224            �           2620    25255 .   course_students set_created_at_course_students    TRIGGER     �   CREATE TRIGGER set_created_at_course_students BEFORE INSERT ON public.course_students FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 G   DROP TRIGGER set_created_at_course_students ON public.course_students;
       public          postgres    false    233    225            �           2620    25257 .   course_teachers set_created_at_course_teachers    TRIGGER     �   CREATE TRIGGER set_created_at_course_teachers BEFORE INSERT ON public.course_teachers FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 G   DROP TRIGGER set_created_at_course_teachers ON public.course_teachers;
       public          postgres    false    226    233            �           2620    25259    courses set_created_at_courses    TRIGGER     |   CREATE TRIGGER set_created_at_courses BEFORE INSERT ON public.courses FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 7   DROP TRIGGER set_created_at_courses ON public.courses;
       public          postgres    false    222    233            �           2620    25261 *   notifications set_created_at_notifications    TRIGGER     �   CREATE TRIGGER set_created_at_notifications BEFORE INSERT ON public.notifications FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 C   DROP TRIGGER set_created_at_notifications ON public.notifications;
       public          postgres    false    228    233            �           2620    25263    roles set_created_at_roles    TRIGGER     x   CREATE TRIGGER set_created_at_roles BEFORE INSERT ON public.roles FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 3   DROP TRIGGER set_created_at_roles ON public.roles;
       public          postgres    false    216    233            �           2620    25265    users set_created_at_users    TRIGGER     x   CREATE TRIGGER set_created_at_users BEFORE INSERT ON public.users FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 3   DROP TRIGGER set_created_at_users ON public.users;
       public          postgres    false    233    218            �           2620    25250 0   academic_periods set_updated_at_academic_periods    TRIGGER     �   CREATE TRIGGER set_updated_at_academic_periods BEFORE UPDATE ON public.academic_periods FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 I   DROP TRIGGER set_updated_at_academic_periods ON public.academic_periods;
       public          postgres    false    233    220            �           2620    25301    alerts set_updated_at_alerts    TRIGGER     z   CREATE TRIGGER set_updated_at_alerts BEFORE UPDATE ON public.alerts FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 5   DROP TRIGGER set_updated_at_alerts ON public.alerts;
       public          postgres    false    233    230            �           2620    25254 $   attendance set_updated_at_attendance    TRIGGER     �   CREATE TRIGGER set_updated_at_attendance BEFORE UPDATE ON public.attendance FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 =   DROP TRIGGER set_updated_at_attendance ON public.attendance;
       public          postgres    false    233    224            �           2620    25256 .   course_students set_updated_at_course_students    TRIGGER     �   CREATE TRIGGER set_updated_at_course_students BEFORE UPDATE ON public.course_students FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 G   DROP TRIGGER set_updated_at_course_students ON public.course_students;
       public          postgres    false    233    225            �           2620    25258 .   course_teachers set_updated_at_course_teachers    TRIGGER     �   CREATE TRIGGER set_updated_at_course_teachers BEFORE UPDATE ON public.course_teachers FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 G   DROP TRIGGER set_updated_at_course_teachers ON public.course_teachers;
       public          postgres    false    226    233            �           2620    25260    courses set_updated_at_courses    TRIGGER     |   CREATE TRIGGER set_updated_at_courses BEFORE UPDATE ON public.courses FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 7   DROP TRIGGER set_updated_at_courses ON public.courses;
       public          postgres    false    222    233            �           2620    25262 *   notifications set_updated_at_notifications    TRIGGER     �   CREATE TRIGGER set_updated_at_notifications BEFORE UPDATE ON public.notifications FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 C   DROP TRIGGER set_updated_at_notifications ON public.notifications;
       public          postgres    false    233    228            �           2620    25264    roles set_updated_at_roles    TRIGGER     x   CREATE TRIGGER set_updated_at_roles BEFORE UPDATE ON public.roles FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 3   DROP TRIGGER set_updated_at_roles ON public.roles;
       public          postgres    false    216    233            �           2620    25266    users set_updated_at_users    TRIGGER     x   CREATE TRIGGER set_updated_at_users BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.set_timestamp();
 3   DROP TRIGGER set_updated_at_users ON public.users;
       public          postgres    false    218    233            �           2606    25187 $   attendance attendance_course_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.attendance
    ADD CONSTRAINT attendance_course_id_fkey FOREIGN KEY (course_id) REFERENCES public.courses(id);
 N   ALTER TABLE ONLY public.attendance DROP CONSTRAINT attendance_course_id_fkey;
       public          postgres    false    224    222    4771            �           2606    25192 %   attendance attendance_student_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.attendance
    ADD CONSTRAINT attendance_student_id_fkey FOREIGN KEY (student_id) REFERENCES public.users(id);
 O   ALTER TABLE ONLY public.attendance DROP CONSTRAINT attendance_student_id_fkey;
       public          postgres    false    4767    218    224            �           2606    25204 .   course_students course_students_course_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.course_students
    ADD CONSTRAINT course_students_course_id_fkey FOREIGN KEY (course_id) REFERENCES public.courses(id);
 X   ALTER TABLE ONLY public.course_students DROP CONSTRAINT course_students_course_id_fkey;
       public          postgres    false    225    4771    222            �           2606    25209 /   course_students course_students_student_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.course_students
    ADD CONSTRAINT course_students_student_id_fkey FOREIGN KEY (student_id) REFERENCES public.users(id);
 Y   ALTER TABLE ONLY public.course_students DROP CONSTRAINT course_students_student_id_fkey;
       public          postgres    false    218    225    4767            �           2606    25221 .   course_teachers course_teachers_course_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.course_teachers
    ADD CONSTRAINT course_teachers_course_id_fkey FOREIGN KEY (course_id) REFERENCES public.courses(id);
 X   ALTER TABLE ONLY public.course_teachers DROP CONSTRAINT course_teachers_course_id_fkey;
       public          postgres    false    4771    226    222            �           2606    25226 /   course_teachers course_teachers_teacher_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.course_teachers
    ADD CONSTRAINT course_teachers_teacher_id_fkey FOREIGN KEY (teacher_id) REFERENCES public.users(id);
 Y   ALTER TABLE ONLY public.course_teachers DROP CONSTRAINT course_teachers_teacher_id_fkey;
       public          postgres    false    218    4767    226            �           2606    25156 '   courses courses_academic_period_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.courses
    ADD CONSTRAINT courses_academic_period_id_fkey FOREIGN KEY (academic_period_id) REFERENCES public.academic_periods(id);
 Q   ALTER TABLE ONLY public.courses DROP CONSTRAINT courses_academic_period_id_fkey;
       public          postgres    false    222    220    4769            �           2606    25243 +   notifications notifications_student_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_student_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);
 U   ALTER TABLE ONLY public.notifications DROP CONSTRAINT notifications_student_id_fkey;
       public          postgres    false    228    4767    218            �           2606    25127    users users_role_id_fkey    FK CONSTRAINT     w   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(id);
 B   ALTER TABLE ONLY public.users DROP CONSTRAINT users_role_id_fkey;
       public          postgres    false    4763    216    218            `   �   x���K�0D��)r�D�q�i�tK�l���@D�@�Ҍl��2'���%@�V�e��e��ڤDT]�����=l��*l\kdeJ]��~y\}��j���LWx1Dj�DXP��+U���kw[t�=B�5      j   �   x�]�=�0�99E�Z��c��]:�1!�/RJ���zz�����?_[�$���uA_�
s� 4�Rی�0F��Ws�Dc���w�@OM���:�[pC���z��#�P�t��4T:�VHBj(�x�&稇(wG���L^!���56      d   �   x�����0�g���l'�3K���nA"�ߧ(��	.'��L�/�/⸮����[��^\�:��x�F�'�A9	
@y����ȩ��Y{����]8:p���c��5D@�d��<�}'��t
A]��g�:vX����>w�8�;T��������!����,E��b�bЃj�s�����`�LL���m��9��w�;������      e   �   x����� ϡ�4���O� �ױH+�M�C$n3�dx���[E��;S4&@&q��y�W䷧�FH��&h�����I�J����cd����i��i�����}Ck ���t�VqJ����=!��Ο�d!b@��(Ck@���%d���>���
�z      f   K   x�}��	�0F�s�"(�j�� ���B����}<k�L-X';��!c`f�h�7��'�udyT�V��D߿���5�      b   �   x���1
�PD����������,����TPs�(�`#l7o���z8O7Q�B�-jkl��Df�j�ψ%��ܓ��m7�}(�T����q����x
��4��"��(��o�Lh�K�Jժ��!> ��T��i������%��0t +�74�i�SJw~�<�      l      x������ � �      h   �   x���;
B1��:YE��L�uӹ;+q��H���L�s�$!�ϧ��X��`YZ���qa���t�����G�5�?�� �KW|н���l�d���N�C�Nɿ�>ѽ68{L�o���ox�G�      \   E   x�3�tL����4202�50�52S00�24�25�*�D\F�!����E$�3�.)MI�+!Q_� 9P#�      ^   Z  x���Ms�0�s�z-�7H��Ң�h�Z��� F�� ���ݾN;�{��%�����O0(E�B5�>ȶ�0B��3|8C�,�w`S-���d'�|=��࢚�]�%���L����7o�m{;����Cv�-BY�i���c����2 "���U�^�0Β2`BM�q"����V"/"��f��t������~��S�ޑ��5m�v�
�� ���>���n�D�w�+��'㼐9�;k�M�&�J���,\$w3O�+.��8_�c鳥����KzC��G�=�:zh�O�8�v5�i��G�1������(E��Q��e�L�`<&B݇AQ&i*�وe�Ij���ݻ��Q׿�Z�r�'�^��*����x�3��[��!���e�sݬ���]7k�܂�~DH��%2���Ԥv_�Lr�����$8�Y T�ĩ��+`��L�?�F���T����a2��Q��}��Z�nU��v���DjD6�(�a{\U� �����c��g��׈�C�#�Pz�LL��_��9b%
���)x��n�T"��"�`�%���v�C�t\�i^7l�6a����7ݖ�i�MӞ/a     