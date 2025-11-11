import styles from './TestButton.module.css'

export default function TestButton({ label = 'Click me', onClick }) {
  return (
    <button className={styles.button} type="button" onClick={onClick}>
      {label}
    </button>
  )
}

