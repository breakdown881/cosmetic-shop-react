const NavIcon = ({ className }) => <i className={className} />;

const SidebarDropdown = ({ icon, isOpen = false, items = [], label }) => (
    <li className={`nav-item dropdown ${isOpen ? 'show' : ''}`}>
        <a className="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded={isOpen ? 'true' : 'false'}>
            <NavIcon className={icon} />
            <span>{label}</span>
        </a>
        <div className={`dropdown-menu ${isOpen ? 'show' : ''}`}>
            {items.map((item) => (
                <a key={`${label}-${item.label}`} className={`dropdown-item ${item.active ? 'active' : ''}`} href={item.href}>
                    {item.label}
                </a>
            ))}
        </div>
    </li>
);

export default function AdminSidebar({ items = [] }) {
    return (
        <ul className="sidebar navbar-nav">
            {items.map((item) => {
                if (item.children?.length) {
                    return (
                        <SidebarDropdown
                            key={item.label}
                            icon={item.icon}
                            isOpen={item.open}
                            items={item.children}
                            label={item.label}
                        />
                    );
                }

                return (
                    <li key={item.label} className={`nav-item ${item.active ? 'active' : ''}`}>
                        <a className="nav-link" href={item.href}>
                            <NavIcon className={item.icon} />
                            <span>{item.label}</span>
                        </a>
                    </li>
                );
            })}
        </ul>
    );
}
