import AuthController from './AuthController'
import LayananController from './LayananController'
import PengajuanController from './PengajuanController'
const Api = {
    AuthController: Object.assign(AuthController, AuthController),
LayananController: Object.assign(LayananController, LayananController),
PengajuanController: Object.assign(PengajuanController, PengajuanController),
}

export default Api