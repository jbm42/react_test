import styles from './NotFound.module.css'

export default function NotFound() {
  return (
    <section class={styles.notFound}>
      <h2>Page Not Found</h2>
      <p>The requested page was not found.</p>
    </section>
  )
}

