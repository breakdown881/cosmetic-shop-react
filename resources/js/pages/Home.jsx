export default function Home({ title = 'React đã sẵn sàng', description = 'Frontend React đang được mount qua Laravel Vite.' }) {
    return (
        <section className="react-home">
            <h2>{title}</h2>
            <p>{description}</p>
        </section>
    );
}
