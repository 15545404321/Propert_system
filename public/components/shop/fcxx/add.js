Vue.component('Add', {
	template: `
		<el-drawer title="添加房屋"  direction="rtl" size="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="楼宇/单元" prop="louyu_id">
							<treeselect v-if="show" :appendToBody="true" :default-expand-level="2" v-model="form.louyu_id" :options="louyu_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择楼宇/单元"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所在楼层" prop="fcxx_szlc">
							<el-input  v-model="form.fcxx_szlc" autoComplete="off" clearable maxlength="4" show-word-limit placeholder="请输入所在楼层"><template slot="prepend">第</template><template slot="append">层</template></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房间编号" prop="fcxx_fjbh">
							<el-input  v-model="form.fcxx_fjbh" autoComplete="off" clearable  placeholder="请输入房间编号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="建筑面积" prop="fcxx_jzmj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fcxx_jzmj" clearable :min="0" placeholder="请输入建筑面积"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="套内面积" prop="fcxx_tnmj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.fcxx_tnmj" clearable :min="0" placeholder="请输入套内面积"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房屋类型" prop="fwlx_id">
							<el-select   style="width:100%" v-model="form.fwlx_id" filterable clearable placeholder="请选择房屋类型">
								<el-option v-for="(item,i) in fwlx_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房产房主" prop="member_id">
							<el-select  remote :remote-method="remoteMemberidList"  style="width:100%" v-model="form.member_id" filterable clearable placeholder="请选择房产房主">
								<el-option v-for="(item,i) in member_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" style="text-align:center;margin:0 0 30px 0">
				<el-button :size="size" style="width:35%;" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" style="width:35%;" @click="closeForm">取 消</el-button>
			</div>
			</div>
		</el-drawer>
	`
	,
	components:{
		'treeselect':VueTreeselect.Treeselect,
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				fcxx_szlc:'',
				fcxx_fjbh:'',
				fwlx_id:'',
				member_id:'',
			},
			louyu_ids:[],
			fwlx_ids:[],
			member_ids:[],
			loading:false,
			rules: {
				louyu_id:[
					{required: true, message: '楼宇/单元不能为空', trigger: 'change'},
				],
				fcxx_szlc:[
					{required: true, message: '所在楼层不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '所在楼层格式错误'}
				],
				fcxx_fjbh:[
					{required: true, message: '房间编号不能为空', trigger: 'blur'},
				],
				fwlx_id:[
					{required: true, message: '房屋类型不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Fcxx/getFieldList').then(res => {
					if(res.data.status == 200){
						this.louyu_ids = res.data.data.louyu_ids
						this.fwlx_ids = res.data.data.fwlx_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
		},
		remoteMemberidList(val){
			axios.post(base_url + '/Fcxx/remoteMemberidList',{queryString:val}).then(res => {
				if(res.data.status == 200){
					this.member_ids = res.data.data
				}
			})
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Fcxx/add',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			this.member_ids = []
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
