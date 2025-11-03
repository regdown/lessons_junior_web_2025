import { AgeProps } from '../interfaces/UserAge';

export function UserAge({age}: AgeProps){
    return(
        <div>
            {(age<18) ? (
                <p>Пользователю <span style={{color:'red'}}>{age}</span> лет</p>
            ) : (
                <p>Пользователю <span  style={{color: 'green'}}>{age}</span> лет</p>
            )}
        </div>
    )
}